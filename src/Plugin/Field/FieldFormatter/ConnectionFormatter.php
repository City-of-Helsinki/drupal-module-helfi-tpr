<?php

namespace Drupal\helfi_tpr\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

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
class ConnectionFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      if (!$item->data) {
        continue;
      }
      /** @var \Drupal\helfi_tpr\Field\Connection\Connection $connection */
      $connection = $item->data;
      $element[$delta] = [
        '#type' => 'item',
        'content' => $connection->build(),
      ];
    }

    return $element;
  }

}
