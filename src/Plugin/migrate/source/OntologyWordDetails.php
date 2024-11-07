<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Plugin\migrate\source;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\helfi_api_base\Plugin\migrate\source\HttpSourcePluginBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Source plugin for retrieving data from Tpr.
 *
 * @MigrateSource(
 *   id = "tpr_ontology_word_details"
 * )
 */
class OntologyWordDetails extends HttpSourcePluginBase implements ContainerFactoryPluginInterface {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * {@inheritdoc}
   */
  protected bool $useRequestCache = FALSE;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ?MigrationInterface $migration = NULL,
  ) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition, $migration);
    $instance->configFactory = $container->get('config.factory');
    return $instance;
  }

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
   * Gets the ontology ids that are included to the migration.
   *
   * Ontology ID limit is set using 'helfi_tpr.limit_ontology_words:ids' config.
   * If the config does not exist, empty array is returned, indicating that the
   * migrate does not limit by ontology ids.
   *
   * @return array
   *   List of ontology ids or empty array.
   */
  public function getOntologyIdsLimit() : array {
    if ($config = $this->configFactory->get('helfi_tpr.limit_ontology_words')) {
      if ($ids = $config->get('ids')) {
        return $ids;
      }
    }

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
   * Combine the two content sources and optionally limit with given IDs.
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
    $ontologyIdsLimit = $this->getOntologyIdsLimit();
    $useLimit = (count($ontologyIdsLimit) !== 0);

    foreach ($content as $contentItem) {
      foreach ($detailedContent as $detailedItem) {
        if ($contentItem['id'] === $detailedItem['ontologyword_id']) {
          // Skip source when limit is set and the ontology ID is not found from
          // the list.
          if ($useLimit && !in_array($contentItem['id'], $ontologyIdsLimit)) {
            continue;
          }

          $id = $detailedItem['ontologyword_id'] . '_' . $detailedItem['unit_id'];

          if (empty($combined[$id])) {
            $combined[$id] = [
              'id' => $id,
              'ontologyword_id' => $detailedItem['ontologyword_id'],
              'unit_id' => $detailedItem['unit_id'],
              'name_fi' => $contentItem['ontologyword_fi'],
              'name_sv' => $contentItem['ontologyword_sv'],
              'name_en' => $contentItem['ontologyword_en'],
              'details' => [],
            ];
          }

          $combined[$id]['details'][] = $detailedItem;
        }
      }
    }

    return $combined;
  }

}
