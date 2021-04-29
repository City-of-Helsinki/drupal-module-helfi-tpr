<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\RevisionLogEntityTrait;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\helfi_api_base\Entity\RemoteEntityBase;

/**
 * Defines the base class for all TPR entities.
 */
abstract class TprEntityBase extends RemoteEntityBase implements RevisionableInterface {

  use RevisionLogEntityTrait;

  /**
   * Creates a basic string field.
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
    return BaseFieldDefinition::create('string')
      // @codingStandardsIgnoreLine
      ->setLabel(new TranslatableMarkup($label))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setDefaultValue('')
      ->setCardinality($cardinality)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'readonly_field_widget',
      ])
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ]);
  }

  /**
   * Creates duplicate overridable fields for given base fields.
   *
   * @param \Drupal\Core\Field\BaseFieldDefinition[] $fields
   *   The field definitions.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition[]
   *   The field definitions.
   */
  protected static function createOverrideFields(array $fields) : array {
    // Create duplicate fields that can be modified by end users and
    // are ignored by migrations.
    $weight = -20;
    foreach ($fields as $name => $field) {
      $field->setDisplayOptions('form', [
        'weight' => $weight++,
        'type' => 'readonly_field_widget',
      ]);
      $override_field = clone $field;
      $override_field
        ->setDisplayConfigurable('view', TRUE)
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayOptions('form', [
          'weight' => $weight++,
        ])
        ->setLabel(
          new TranslatableMarkup('Override: @field_name', [
            '@field_name' => $field->getLabel(),
          ])
        );
      $fields[sprintf('%s_override', $name)] = $override_field;
    }

    return $fields;
  }

  /**
   * Creates a basic link field.
   *
   * @param string $label
   *   The label.
   * @param int $cardinality
   *   The cardinality.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   The field definition.
   */
  protected static function createLinkField(string $label, int $cardinality = 1) : BaseFieldDefinition {
    return BaseFieldDefinition::create('link')
      // @codingStandardsIgnoreLine
      ->setLabel(new TranslatableMarkup($label))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setDefaultValue('')
      ->setCardinality($cardinality)
      ->setDisplayOptions('form', [
        'type' => 'readonly_field_widget',
      ])
      ->setSettings([
        'max_length' => 255,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::revisionLogBaseFieldDefinitions($entity_type);
    $fields['name'] = static::createStringField('Name');

    foreach (['changed', 'created'] as $field) {
      // All translations should have same date.
      $fields[$field]->setTranslatable(FALSE);
    }

    return $fields;
  }

}
