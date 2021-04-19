<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\destination;

use Drupal\helfi_api_base\Plugin\migrate\destination\TranslatableEntityBase;

/**
 * Provides a destination plugin for Tpr service channel entities.
 *
 * @MigrateDestination(
 *   id = "tpr_service_channel",
 * )
 */
final class ServiceChannel extends TranslatableEntityBase {

  /**
   * {@inheritdoc}
   */
  protected static function getEntityTypeId($plugin_id) {
    return 'tpr_service_channel';
  }

}
