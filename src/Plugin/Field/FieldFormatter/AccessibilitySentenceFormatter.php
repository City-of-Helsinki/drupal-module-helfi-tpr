<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'Accessibility sentence' formatter.
 *
 * @FieldFormatter(
 *   id = "tpr_accessibility_sentence",
 *   label = @Translation("Accessibibility sentences"),
 *   field_types = {
 *     "tpr_accessibility_sentence"
 *   }
 * )
 */
final class AccessibilitySentenceFormatter extends FormatterBase {

  /**
   * Groups item by group label.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The items to be grouped.
   *
   * @return array
   *   The grouped items.
   */
  private function groupItemsByLabel(FieldItemListInterface $items) : array {
    $index = 0;
    $mapping = [];

    foreach ($items as $item) {
      if (!isset($mapping[$item->group])) {
        $mapping[$item->group] = [
          'label' => $item->group,
          'index' => $index++,
          'items' => [],
        ];
      }
      $mapping[$item->group]['items'][] = $item->value;
    }
    return $mapping;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) : array {
    /** @var \Drupal\helfi_tpr\Plugin\Field\FieldType\AccessibilitySentenceItem[] $items */
    $element = [];

    foreach ($this->groupItemsByLabel($items) as $group) {
      ['label' => $label, 'index' => $i] = $group;

      if (!isset($element[$i])) {
        $element[$i] = [
          '#theme' => 'tpr_accessibility_sentences',
          '#name' => $label,
          '#items' => [],
        ];
      }

      foreach ($group['items'] as $item) {
        $element[$i]['#items'][] = $item;
      }
    }

    return $element;
  }

}
