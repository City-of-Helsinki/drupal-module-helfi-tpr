<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'tpr_ontology_word_detail_item' field type.
 *
 * @FieldType(
 *   id = "tpr_ontology_word_detail_item",
 *   label = @Translation("Ontology word detail item"),
 *   no_ui = TRUE,
 *   default_formatter = "tpr_ontology_word_detail_item"
 * )
 */
class OntologyWordDetailItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public function isEmpty() : bool {
    $clarification = $this->get('clarification')->getValue();
    $schoolyear = $this->get('schoolyear')->getValue();
    return empty($clarification) && empty($schoolyear);
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) : array {
    $properties['clarification'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Clarification'))
      ->setRequired(FALSE);
    $properties['schoolyear'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Schoolyear'))
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) : array {
    $columns = [
      'clarification' => [
        'type' => 'text',
        'size' => 'big',
      ],
      'schoolyear' => [
        'type' => 'varchar',
        'length' => 255,
      ],
    ];

    return ['columns' => $columns];
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) : array {
    $random = new Random();
    foreach (['clarification', 'schoolyear'] as $key) {
      $values[$key] = $random->word(mt_rand(1, 50));
    }
    return $values;
  }

}
