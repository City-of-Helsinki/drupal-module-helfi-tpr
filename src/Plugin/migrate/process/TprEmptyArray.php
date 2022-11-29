<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Check if empty return false else true.
 *
 * @MigrateProcessPlugin(
 *   id = "tpr_empty_array"
 * )
 *
 * To return true or false use the following:
 *
 * @code
 * field_text:
 *   plugin: tpr_empty_array
 *   source: array
 * @endcode
 */
class TprEmptyArray extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) : bool {
    return !empty($value);
  }

}
