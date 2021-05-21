<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\helfi_tpr\Field\Connection\Connection;
use Drupal\helfi_tpr\Field\Connection\OpeningHour;

/**
 * Defines the 'tpr_connection' field type.
 *
 * @FieldType(
 *   id = "tpr_connection",
 *   label = @Translation("Connection"),
 *   no_ui = TRUE,
 *   default_formatter = "tpr_connection"
 * )
 */
class ConnectionItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public function isEmpty() : bool {
    return empty($this->value) || empty($this->type) || empty($this->data);
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName() : ? string {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) : array {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Value'))
      ->setRequired(TRUE);
    $properties['type'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Type'))
      ->setRequired(TRUE);
    $properties['data'] = DataDefinition::create('tpr_connection_data')
      ->setLabel(new TranslatableMarkup('Data'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($values, $notify = TRUE) {
    // Populate from object if passed one.
    if ($values instanceof Connection) {
      $values = [
        'value' => $values->get('name'),
        'type' => $values::TYPE_NAME,
        'data' => $values,
      ];
    }
    parent::setValue($values, $notify);
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
      'type' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'length' => 255,
      ],
      'data' => [
        'type' => 'blob',
        'size' => 'big',
        'serialize' => TRUE,
      ],
    ];

    return ['columns' => $columns];
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) : array {
    $random = new Random();
    $object = (new OpeningHour())
      ->set('name', $random->word(mt_rand(1, 50)))
      ->set('www', 'https://' . $random->word(mt_rand(1, 50)) . '.com/');

    return [
      'value' => $object->get('name'),
      'type' => 'OPENING_HOURS',
      'data' => $object,
    ];
  }

}
