<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\destination;

use Drupal\helfi_api_base\Entity\RemoteEntityBase;
use Drupal\helfi_tpr\Entity\Unit;
use Drupal\migrate\Row;

/**
 * Provides a destination plugin for Tpr service entities.
 *
 * @MigrateDestination(
 *   id = "tpr_service",
 * )
 */
final class Service extends TprDestinationBase {

  /**
   * {@inheritdoc}
   */
  protected static function getEntityTypeId($plugin_id) : string {
    return 'tpr_service';
  }

  /**
   * {@inheritdoc}
   */
  public function getEntity(Row $row, array $old_destination_id_values) : RemoteEntityBase {
    $entity = parent::getEntity($row, $old_destination_id_values);

    // Unit ids are not language specific so we can safely return early
    // if we're not saving the first translation.
    if (!$row->getSourceProperty('default_langcode')) {
      return $entity;
    }

    if ($unitIds = $row->getSourceProperty('unit_ids')) {
      $units = Unit::loadMultiple($unitIds);

      array_map(function (Unit $unit) use ($entity) {
        $unit->addService($entity)
          ->save();
      }, $units);
    }

    return $entity;
  }

}
