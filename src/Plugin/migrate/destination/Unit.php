<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\destination;

/**
 * Provides a destination plugin for Tpr entities.
 *
 * @MigrateDestination(
 *   id = "tpr_unit",
 * )
 */
final class Unit extends Tpr {

  /**
   * {@inheritdoc}
   */
  protected function getTranslatableFields(): array {
    return [
      'name',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected static function getEntityTypeId($plugin_id) {
    return 'tpr_unit';
  }

}
