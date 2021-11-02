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
 * Defines the 'tpr_accessibility_sentence' field type.
 *
 * @FieldType(
 *   id = "tpr_accessibility_sentence",
 *   label = @Translation("AccessibilitySentence"),
 *   no_ui = TRUE,
 *   default_formatter = "tpr_accessibility_sentence"
 * )
 */
class AccessibilitySentenceItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public function isEmpty() : bool {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) : array {
    $properties['group'] = DataDefinition::create('string')
      ->setLabel((string) new TranslatableMarkup('Group'))
      ->setRequired(TRUE);
    $properties['value'] = DataDefinition::create('string')
      ->setLabel((string) new TranslatableMarkup('Value'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) : array {
    $columns = [
      'value' => [
        'type' => 'text',
        'size' => 'big',
      ],
      'group' => [
        'type' => 'varchar',
        'not null' => FALSE,
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
    foreach (['value', 'group'] as $key) {
      $values[$key] = $random->word(mt_rand(1, 50));
    }
    return $values;
  }

}
