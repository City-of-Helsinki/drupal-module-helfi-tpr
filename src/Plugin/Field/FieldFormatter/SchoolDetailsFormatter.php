<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'School details' formatter.
 *
 * @FieldFormatter(
 *   id = "tpr_school_details",
 *   label = @Translation("School details"),
 *   field_types = {
 *     "tpr_school_details"
 *   }
 * )
 */
final class SchoolDetailsFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) : array {
    /** @var \Drupal\helfi_tpr\Plugin\Field\FieldType\SchoolDetailsItem[] $items */
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#theme' => 'tpr_school_details',
        '#clarification' => $item->get('clarification')->getValue(),
        '#schoolyear' => $item->get('schoolyear')->getValue(),
      ];
    }

    return $elements;
  }

}
