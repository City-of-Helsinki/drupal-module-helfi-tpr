<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\process;

use Drupal\helfi_tpr\Entity\Unit;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Check unit's published status by ID and return it.
 *
 * @MigrateProcessPlugin(
 *   id = "publish_by_unit"
 * )
 *
 * @code
 * content_translation_status:
 *   plugin: publish_by_unit
 *   source: unit_id
 * @endcode
 */
class PublishByUnit extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $unit = Unit::load($value);
    if (!empty($unit) && $unit->isPublished()) {
      return '1';
    }
    return '0';
  }

}
