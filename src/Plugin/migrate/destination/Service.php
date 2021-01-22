<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\destination;

/**
 * Provides a destination plugin for Tpr service entities.
 *
 * @MigrateDestination(
 *   id = "tpr_service",
 * )
 */
final class Service extends TranslatableEntityBase {

  /**
   * {@inheritdoc}
   */
  protected static function getEntityTypeId($plugin_id) {
    return 'tpr_service';
  }

  /**
   * {@inheritdoc}
   */
  protected function getTranslatableFields(): array {
    return [
      'title' => 'name',
      'description_long' => 'description/value',
      'description_short' => 'description/summary',
    ];
  }

}
