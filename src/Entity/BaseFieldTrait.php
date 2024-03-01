<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * A helper trait to create base fields.
 */
trait BaseFieldTrait {

  /**
   * Helper function to create a basic string field.
   *
   * @param string $label
   *   The label.
   * @param int $cardinality
   *   The cardinality.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   The field definition.
   */
  protected static function createStringField(string $label, int $cardinality = 1) : BaseFieldDefinition {
    return static::createBaseField(BaseFieldDefinition::create('string'), $label)
      // @codingStandardsIgnoreLine
      ->setCardinality($cardinality)
      ->setDefaultValue('')
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ]);
  }

  /**
   * Helper function to create a basic base field.
   *
   * @param \Drupal\Core\Field\BaseFieldDefinition $field
   *   The base field.
   * @param string $label
   *   The label.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   The base field.
   */
  protected static function createBaseField(BaseFieldDefinition $field, string $label) : BaseFieldDefinition {
    return $field
      // @codingStandardsIgnoreLine
      ->setLabel(new TranslatableMarkup($label))
      ->setTranslatable(TRUE)
      ->setRevisionable(FALSE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'readonly_field_widget',
      ]);
  }

  /**
   * Helper function to create a basic phone field.
   *
   * @param string $label
   *   The label.
   * @param int $cardinality
   *   The cardinality.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   The field definition.
   */
  protected static function createPhoneField(string $label, int $cardinality = 1) : BaseFieldDefinition {
    return static::createBaseField(BaseFieldDefinition::create('telephone'), $label)
      ->setCardinality($cardinality);
  }

}
