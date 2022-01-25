<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\source;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\helfi_api_base\Plugin\migrate\source\HttpSourcePluginBase;

/**
 * Source plugin for retrieving data from Tpr.
 *
 * @MigrateSource(
 *   id = "tpr_ontology_word_details"
 * )
 */
class OntologyWordDetails extends HttpSourcePluginBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  protected bool $useRequestCache = FALSE;

  /**
   * Include source data that has school details.
   *
   * @var bool
   */
  protected bool $includeSchoolDetails = TRUE;

  /**
   * Include source data with selected ontology IDs.
   *
   * @var int[]
   */
  protected array $includeOntologyIds = [
    816,
    650,
    590,
  ];

  /**
   * {@inheritdoc}
   */
  public function __toString() : string {
    return 'OntologyWordDetails';
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() : array {
    return [
      'ontologyword_id' => [
        'type' => 'string',
        'entity_key' => 'ontologyword_id',
      ],
      'unit_id' => [
        'type' => 'string',
        'entity_key' => 'unit_id',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function fields() : array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeListIterator() : \Iterator {
    $originalContent = $this->getContent($this->configuration['url']);
    $detailedContent = $this->getContent($this->configuration['details_url']);
    $content = $this->combineContentAndDetails($originalContent, $detailedContent);

    $processed = 0;

    foreach ($content as $item) {
      $processed++;
      // Allow number of items to be limited by using an env variable.
      if (($this->getLimit() > 0) && $processed > $this->getLimit()) {
        break;
      }
      yield from $this->normalizeMultilingualData($item);
    }
  }

  /**
   * Converts one multilingual object into multiple objects.
   *
   * @param array $data
   *   The data from API.
   *
   * @return \Generator
   *   The iterator.
   */
  protected function normalizeMultilingualData(array $data) : \Generator {
    foreach (['fi', 'sv', 'en'] as $language) {
      // Skip translations without translated names.
      if (!isset($data[sprintf('name_%s', $language)])) {
        continue;
      }
      $item = $data;

      if ($language === 'fi') {
        // Always use Finnish as default language.
        $item['default_langcode'] = TRUE;
      }
      $item['language'] = $language;

      // 'Normalize' suffixed fields to have same name for every language.
      // For example: name_fi, name_sv => name.
      foreach ($this->configuration['translatable_fields'] ?? [] as $field) {
        $key = sprintf('%s_%s', $field, $language);
        if (!isset($data[$key])) {
          continue;
        }
        $item[$field] = $data[$key];
      }

      // 'Normalize' suffixed details fields.
      foreach ($this->configuration['translatable_details_fields'] ?? [] as $detailsField) {
        $clarificationKey = sprintf('%s_%s', $detailsField, $language);
        foreach ($data['details'] as $detailsKey => $detailsItem) {
          if (!isset($detailsItem[$clarificationKey])) {
            continue;
          }
          $item['details'][$detailsKey][$detailsField] = trim($detailsItem[$clarificationKey]);
        }
      }

      yield $item;
    }
  }

  /**
   * Combine the two content sources.
   *
   * @param array $content
   *   The source JSON content.
   * @param array $detailedContent
   *   The source JSON content.
   *
   * @return array
   *   The new source content.
   */
  private function combineContentAndDetails(array $content, array $detailedContent): array {
    $combined = [];

    foreach ($content as $contentKey => $contentItem) {
      foreach ($detailedContent as $detailedKey => $detailedItem) {
        if ($contentItem['id'] === $detailedItem['ontologyword_id']) {
          // Only process pre-selected source data.
          $process = FALSE;
          if ($this->includeSchoolDetails ? $this->hasSchoolDetails($detailedItem) : FALSE) {
            $process = TRUE;
          }
          if (!empty($this->includeOntologyIds) ? in_array($contentItem['id'], $this->includeOntologyIds) : FALSE) {
            $process = TRUE;
          }
          if (!$process) {
            continue;
          }

          $id = $detailedItem['ontologyword_id'] . '_' . $detailedItem['unit_id'];

          if (empty($combined[$id])) {
            $combined[$id] = [
              'id' => $id,
              'ontologyword_id' => $detailedItem['ontologyword_id'],
              'unit_id' => $detailedItem['unit_id'],
              'name_fi' => $detailedItem['unit_id'] . ': ' . $contentItem['ontologyword_fi'],
              'name_sv' => $detailedItem['unit_id'] . ': ' . $contentItem['ontologyword_sv'],
              'name_en' => $detailedItem['unit_id'] . ': ' . $contentItem['ontologyword_en'],
              'details' => [],
            ];
          }

          $combined[$id]['details'][] = $detailedItem;
        }
      }
    }

    return $combined;
  }

  /**
   * Checks if the array has school details.
   *
   * @param array $item
   *   Array to check the school detail keys.
   *
   * @return bool
   *   TRUE if school details exist.
   */
  private function hasSchoolDetails(array $item): bool {
    if (!isset($item['schoolyear']) ||
      !(isset($item['clarification_fi']) ||
        isset($item['clarification_sv']) ||
        isset($item['clarification_en']))) {
      return FALSE;
    }
    return TRUE;
  }

}
