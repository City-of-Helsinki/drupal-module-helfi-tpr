<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Only include array when element (with a given key) matches a given value.
 *
 * @MigrateProcessPlugin(
 *   id = "array_element_equals"
 * )
 *
 * @code
 * opening_hours_connections:
 *   plugin: array_element_equals
 *   source: connections
 *   value: OPENING_HOURS
 *   key: type
 * @endcode
 */
class ArrayElementEquals extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    if (empty($configuration['value']) && !array_key_exists('value', $configuration)) {
      throw new \InvalidArgumentException('ArrayElementEquals plugin is missing value configuration.');
    }
    if (empty($configuration['key']) && !array_key_exists('key', $configuration)) {
      throw new \InvalidArgumentException('ArrayElementEquals plugin is missing key configuration.');
    }
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $matchValue = $this->configuration['value'];
    $key = $this->configuration['key'];

    if (is_array($value) || $value instanceof \Traversable) {
      if (!empty($value[$key]) && $value[$key] === $matchValue) {
        return $value;
      }
    }

    return [];
  }

}
