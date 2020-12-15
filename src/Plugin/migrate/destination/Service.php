<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\destination;

/**
 * Provides a destination plugin for Tpr entities.
 *
 * @MigrateDestination(
 *   id = "tpr_service",
 * )
 */
final class Service extends TprServiceMap {

  /**
   * {@inheritdoc}
   */
  protected function getTranslatableFields(): array {
    return [
      'name' => 'name',
      'call_charge_info' => 'call_charge_info',
      'www' => 'www/uri',
      'address_postal_full' => 'address_postal',
      'street_address' => 'address/address_line1',
      'address_city' => 'address/locality',
      'desc' => 'description/value',
      'short_desc' => 'description/summary',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected static function getEntityTypeId($plugin_id) {
    return 'tpr_service';
  }

}
