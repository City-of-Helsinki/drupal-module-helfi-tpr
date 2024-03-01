<?php

declare(strict_types=1);

namespace Drupal\helfi_address_search\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;

/**
 * Field handler for distance.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("distance")
 */
class DistanceField extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do not query.
  }

}
