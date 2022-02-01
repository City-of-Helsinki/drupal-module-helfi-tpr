<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\source;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Source plugin for retrieving data from Tpr.
 *
 * @MigrateSource(
 *   id = "tpr_service_map"
 * )
 */
class ServiceMap extends TprSourceBase implements ContainerFactoryPluginInterface {

  use ServiceMapTrait;

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
    return 'TprServiceMap';
  }

  /**
   * {@inheritdoc}
   */
  public function count($refresh = FALSE) {
    if (!$this->count) {
      $this->count = count($this->getContent($this->configuration['url']));
    }
    return $this->count;
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeSingleImportIterator(): \Iterator {
    foreach ($this->entityIds as $entityId) {
      $content = $this->getContent($this->buildCanonicalUrl($entityId));

      yield from $this->normalizeMultilingualData($content);
    }
  }

  /**
   * Gets the extra unit data such as connections and accessibility sentences.
   *
   * @param int $unitId
   *   The unit id used to get the data.
   * @param string $type
   *   The type to fetch.
   * @param string $url
   *   The url to fetch.
   *
   * @return array
   *   The data.
   */
  protected function getExtraUnitData(int $unitId, string $type, string $url) : array {
    static $data;

    if (!isset($data[$type])) {
      $content = $this->getContent($url);

      foreach ($content as $object) {
        if (!isset($object['unit_id'])) {
          continue;
        }
        $data[$type][$object['unit_id']][] = $object;
      }
    }
    return $data[$type][$unitId] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeListIterator() : \Iterator {
    $content = $this->getContent($this->configuration['url']);

    $dates = [];
    // Sort data by modified_time.
    foreach ($content as $key => $item) {
      if (!isset($item['modified_time'])) {
        $item['modified_time'] = (new DateTimePlus())->format('Y-m-d\TH:i:s');
      }
      $dates[$key] = DateTimePlus::createFromFormat('Y-m-d\TH:i:s', $item['modified_time'])->format('U');
    }
    array_multisort($dates, SORT_DESC, $content);

    $processed = 0;

    foreach ($content as $object) {
      $processed++;

      // Allow number of items to be limited by using an env variable.
      if (($this->getLimit() > 0) && $processed > $this->getLimit()) {
        break;
      }
      // Skip entire migration once we've reached the number of maximum
      // ignored (not changed) rows.
      // @see static::NUM_IGNORED_ROWS_BEFORE_STOPPING.
      if ($this->isPartialMigrate() && ($this->ignoredRows >= static::NUM_IGNORED_ROWS_BEFORE_STOPPING)) {
        break;
      }

      $extraData = [
        'accessibility_sentences_url' => 'accessibility_sentences',
        'connections_url' => 'connections',
        'services_url' => 'services',
      ];

      // Combine all extra unit data from other endpoints into one.
      foreach ($extraData as $urlKey => $keyName) {
        if (!isset($this->configuration[$urlKey])) {
          continue;
        }
        $object[$keyName] = $this->getExtraUnitData(
          $object['id'],
          $keyName,
          $this->configuration[$urlKey]
        );
      }

      // Flatten services.
      if (isset($object['services'])) {
        $object['services'] = NestedArray::getValue(
          $object['services'],
          [0, 'services']
        );
      }

      yield from $this->normalizeMultilingualData($object);
    }
  }

}
