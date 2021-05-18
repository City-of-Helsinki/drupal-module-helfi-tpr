<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\RevisionLogEntityTrait;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\helfi_api_base\Entity\RemoteEntityBase;

/**
 * Defines the base class for all TPR entities.
 */
abstract class TprEntityBase extends RemoteEntityBase implements RevisionableInterface, RevisionLogInterface {

  use RevisionLogEntityTrait;
  use BaseFieldTrait;

  /**
   * An array of overridable fields.
   *
   * These are fields that needs to be duplicated and
   * be overridable by the end user.
   *
   * @var \Drupal\Core\Field\BaseFieldDefinition[]
   */
  protected static array $overrideFields = [];

  /**
   * {@inheritdoc}
   */
  public function label() {
    // Use overridden name field as default label when possible.
    if (!$this->get('name_override')->isEmpty()) {
      return $this->get('name_override')->value;
    }
    return parent::label();
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
        ->setRevisionable(TRUE)
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
   * Helper function to create a basic link field.
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
    return static::createBaseField(BaseFieldDefinition::create('link'), $label)
      ->setCardinality($cardinality)
      ->setSettings([
        'max_length' => 255,
      ]);
  }

  /**
   * Whether the entity is published.
   *
   * @return bool
   *   Whether entity is published or not.
   */
  public function isPublished() : bool {
    return (bool) $this->get('content_translation_status')->value;
  }

  /**
   * Gets the author.
   *
   * @return \Drupal\Core\Session\AccountInterface|null
   *   The account.
   */
  public function getAuthor() : ? AccountInterface {
    return $this->get('content_translation_uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::revisionLogBaseFieldDefinitions($entity_type);

    static::$overrideFields['name'] = static::createStringField('Name');

    // Add overridable fields as base fields.
    $fields += static::$overrideFields;

    // Create duplicate fields that can be modified by end users and
    // are ignored by migrations.
    $fields += static::createOverrideFields(static::$overrideFields);

    foreach (['changed', 'created'] as $field) {
      // All translations should have same date.
      $fields[$field]->setTranslatable(FALSE);
    }

    return $fields;
  }

}
