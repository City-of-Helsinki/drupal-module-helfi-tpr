<?php

namespace Drupal\helfi_tpr\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\helfi_tpr\Field\Connection\Connection;

/**
 * Plugin implementation of the 'Connection' formatter.
 *
 * @FieldFormatter(
 *   id = "tpr_connection",
 *   label = @Translation("Connection"),
 *   field_types = {
 *     "tpr_connection"
 *   }
 * )
 */
final class ConnectionFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) : array {
    $element = [];

    foreach ($items as $delta => $item) {
      if (!isset($item->data) || !$item->data instanceof Connection) {
        continue;
      }
      $element[$delta] = [
        '#type' => 'item',
        'content' => $item->data->build(),
        '#parents' => [$delta],
      ];
    }

    return $element;
  }

}
