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
   * The total count.
   *
   * @var int
   */
  protected int $count = 0;

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
      ],
      'unit_id' => [
        'type' => 'string',
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
  public function count($refresh = FALSE) : int {
    if (!$this->count) {
      $originalContent = $this->getContent($this->configuration['url']);
      $detailedContent = $this->getContent($this->configuration['details_url']);
      $content = $this->combineWithDetails($originalContent, $detailedContent);

      $this->count = count($content);
    }
    return $this->count;
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeListIterator() : \Iterator {
    $originalContent = $this->getContent($this->configuration['url']);
    $detailedContent = $this->getContent($this->configuration['details_url']);
    $content = $this->combineWithDetails($originalContent, $detailedContent);

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
    $delta = 0;

    foreach (['fi', 'sv', 'en'] as $language) {
      // Skip translations without translated names.
      if (!isset($data[sprintf('name_%s', $language)])) {
        continue;
      }
      $item = $data;

      // Mark first item as default langcode.
      if ($delta === 0) {
        $item['default_langcode'] = TRUE;
      }
      $delta++;

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

      yield $item;
    }
  }

  private function combineWithDetails(array $content, array $detailedContent): array {
    $content = $this->removeNonExpandableContent($content);
    $detailedContent = $this->removeDetaillessContent($detailedContent);

    $combined = [];
    foreach ($content as $contentKey => $contentItem) {
      foreach ($detailedContent as $detailedKey => $detailedItem) {
        if ($contentItem['id'] === $detailedItem['ontologyword_id']) {
          $id = $detailedItem['ontologyword_id'] . '_' . $detailedItem['unit_id'];

          if (empty($combined[$id])) {
            $combined[$id] = [
              'id' => $id,
              'ontologyword_id' => $detailedItem['ontologyword_id'],
              'unit_id' => $detailedItem['unit_id'],
              'name_fi' => $contentItem['ontologyword_fi'] . ' – ' . $detailedItem['unit_id'],
              'name_sv' => $contentItem['ontologyword_sv'] . ' – ' . $detailedItem['unit_id'],
              'name_en' => $contentItem['ontologyword_en'] . ' – ' . $detailedItem['unit_id'],
              'details' => []
            ];
          }

          $combined[$id]['details'][] = $detailedItem;
        }
      }
    }

    return $combined;
  }

  /*
   * @todo: Remove this later?
   */
  private function removeNonExpandableContent(array $content): array {
    foreach ($content as $key => $item) {
      if ($item['can_add_schoolyear'] !== TRUE || $item['can_add_clarification'] !== TRUE) {
        unset($content[$key]);
      }
    }
    return $content;
  }

  /*
   * @todo: Remove this later?
   */
  private function removeDetaillessContent(array $content): array {
    foreach ($content as $key => $item) {
      // @todo Make this more robust.
      if (!isset($item['schoolyear']) || !(isset($item['clarification_fi']) || isset($item['clarification_sv']) || isset($item['clarification_en']))) {
        unset($content[$key]);
      }
    }
    return $content;
  }

}
