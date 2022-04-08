<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'Ontology word detail item' formatter.
 *
 * @FieldFormatter(
 *   id = "tpr_ontology_word_detail_item",
 *   label = @Translation("Ontology word detail item"),
 *   field_types = {
 *     "tpr_ontology_word_detail_item"
 *   }
 * )
 */
final class OntologyWordDetailFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) : array {
    /** @var \Drupal\helfi_tpr\Plugin\Field\FieldType\OntologyWordDetailItem[] $items */
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#theme' => 'tpr_ontology_word_detail_item',
        '#clarification' => $item->get('clarification')->getValue(),
        '#schoolyear' => $item->get('schoolyear')->getValue(),
      ];
    }

    return $elements;
  }

}
