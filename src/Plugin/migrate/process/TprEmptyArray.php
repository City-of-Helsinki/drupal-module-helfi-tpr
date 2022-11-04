<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\process;

use Drupal\Component\Serialization\Json;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform custom value transformations.
 *
 * @MigrateProcessPlugin(
 *   id = "tpr_empty_array"
 * )
 *
 * To do custom value transformations use the following:
 *
 * @code
 * field_text:
 *   plugin: tpr_empty_array
 *   source: array
 * @endcode
 *
 */
class TprEmptyArray extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value)) {
      return FALSE;
    }
    else {
      return TRUE;
    }
  }
}
