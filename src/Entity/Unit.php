<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Entity;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\FieldOverride;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Webmozart\Assert\Assert;

/**
 * Defines the tpr_unit entity class.
 *
 * @ContentEntityType(
 *   id = "tpr_unit",
 *   label = @Translation("TPR - Unit"),
 *   label_collection = @Translation("TPR - Unit"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\helfi_tpr\Entity\Listing\ListBuilder",
 *     "views_data" = "Drupal\helfi_tpr\TprViewsData",
 *     "access" = "Drupal\entity\EntityAccessControlHandler",
 *     "permission_provider" = "Drupal\entity\EntityPermissionProvider",
 *     "translation" = "Drupal\helfi_tpr\Entity\TranslationHandler",
 *     "form" = {
 *       "default" = "Drupal\helfi_tpr\Entity\Form\ContentEntityForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\helfi_api_base\Entity\Routing\EntityRouteProvider",
 *       "revision" = "\Drupal\helfi_api_base\Entity\Routing\RevisionRouteProvider",
 *     },
 *     "local_action_provider" = {
 *       "collection" = "\Drupal\entity\Menu\EntityCollectionLocalActionProvider",
 *     },
 *     "local_task_provider" = {
 *       "default" = "\Drupal\entity\Menu\DefaultEntityLocalTaskProvider",
 *     },
 *   },
 *   base_table = "tpr_unit",
 *   data_table = "tpr_unit_field_data",
 *   revision_table = "tpr_unit_revision",
 *   revision_data_table = "tpr_unit_field_revision",
 *   show_revision_ui = TRUE,
 *   revisionable = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer tpr_unit",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "langcode" = "langcode",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "published" = "content_translation_status",
 *     "owner" = "content_translation_uid",
 *   },
 *   revision_metadata_keys = {
 *     "revision_created" = "revision_timestamp",
 *     "revision_user" = "revision_user",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "canonical" = "/tpr-unit/{tpr_unit}",
 *     "edit-form" = "/admin/content/integrations/tpr-unit/{tpr_unit}/edit",
 *     "collection" = "/admin/content/integrations/tpr-unit",
 *     "version-history" = "/admin/content/integrations/tpr-unit/{tpr_unit}/revisions",
 *     "revision" = "/tpr-unit/{tpr_unit}/revisions/{tpr_unit_revision}/view",
 *     "revision-revert-language-form" = "/admin/content/integrations/tpr-unit/{tpr_unit}/revisions/{tpr_unit_revision}/revert/{langcode}",
 *   },
 *   field_ui_base_route = "tpr_unit.settings"
 * )
 */
class Unit extends TprEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function getMigration(): ?string {
    return 'tpr_unit';
  }

  /**
   * Adds the given service.
   *
   * @param \Drupal\helfi_tpr\Entity\Service $service
   *   The service.
   *
   * @return $this
   *   The self.
   */
  public function addService(Service $service) : self {
    if (!$this->hasService($service)) {
      $this->get('services')->appendItem($service);
    }
    return $this;
  }

  /**
   * Removes the given service.
   *
   * @param \Drupal\helfi_tpr\Entity\Service $service
   *   The service.
   *
   * @return $this
   *   The self.
   */
  public function removeService(Service $service) : self {
    $index = $this->getServiceIndex($service);
    if ($index !== FALSE) {
      $this->get('services')->offsetUnset($index);
    }
    return $this;
  }

  /**
   * Checks whether the service exists or not.
   *
   * @param \Drupal\helfi_tpr\Entity\Service $service
   *   The service.
   *
   * @return bool
   *   Whether we have given service or not.
   */
  public function hasService(Service $service) : bool {
    return $this->getServiceIndex($service) !== FALSE;
  }

  /**
   * Gets the index of the given service.
   *
   * @param \Drupal\helfi_tpr\Entity\Service $service
   *   The service.
   *
   * @return int|bool
   *   The index of the given service, or FALSE if not found.
   */
  protected function getServiceIndex(Service $service) {
    $values = $this->get('services')->getValue();
    $ids = array_map(function ($value) {
      return $value['target_id'];
    }, $values);

    return array_search($service->id(), $ids);
  }

  /**
   * Gets the picture url.
   *
   * @return string|null
   *   The picture url.
   */
  public function getPictureUrl() : ? string {
    /** @var \Drupal\media\MediaInterface $picture_url */
    $picture_url = $this->get('picture_url_override')->entity;

    // Fallback to default picture url if override is not set.
    if (!$picture_url) {
      return $this->get('picture_url')->value;
    }

    /** @var \Drupal\file\FileInterface $file */
    if ($file = $picture_url->get('field_media_image')->entity) {
      try {
        return $file->createFileUrl(FALSE) ?: NULL;
      }
      catch (\Exception) {
      }
    }
    return NULL;
  }

  /**
   * Gets the description.
   *
   * @return string|null
   *   The description.
   */
  public function getDescription(string $key) : ? string {
    Assert::oneOf($key, ['value', 'summary', 'format']);
    return $this->get('description')->{$key};
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) : array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['picture_url'] = static::createStringField('Picture')
      ->setTranslatable(FALSE);
    $fields['picture_url']->setSetting('max_length', 2048)
      ->setDisplayOptions('form', $fields['picture_url']->getDisplayOptions('form') + [
        'weight' => -19,
      ]);
    $fields['picture_url_override'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Override: Picture'))
      ->setSettings([
        'target_type' => 'media',
        'handler_settings' => [
          'target_bundles' => ['image'],
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'media_library_widget',
        'weight' => -19,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);
    $fields['phone'] = static::createPhoneField('Phone', BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setTranslatable(FALSE);
    $fields['email'] = static::createStringField('Email')
      ->setTranslatable(FALSE);
    $fields['accessibility_phone'] = static::createStringField('Accessibility phone')
      ->setTranslatable(FALSE);
    $fields['accessibility_email'] = static::createStringField('Accessibility email')
      ->setTranslatable(FALSE);
    $fields['www'] = static::createLinkField('Website link')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'link',
      ]);
    $fields['accessibility_www'] = static::createLinkField('Accessibility website link')
      ->setTranslatable(FALSE);
    $fields['description'] = BaseFieldDefinition::create('text_with_summary')
      ->setTranslatable(TRUE)
      ->setRevisionable(FALSE)
      ->setLabel(new TranslatableMarkup('Description'))
      ->setDisplayOptions('form', [
        'type' => 'readonly_field_widget',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['address'] = BaseFieldDefinition::create('address')
      ->setLabel(new TranslatableMarkup('Address'))
      ->setTranslatable(TRUE)
      ->setRevisionable(FALSE)
      ->setSetting('field_overrides', [
        AddressField::GIVEN_NAME => ['override' => FieldOverride::HIDDEN],
        AddressField::ADDITIONAL_NAME => ['override' => FieldOverride::HIDDEN],
        AddressField::FAMILY_NAME => ['override' => FieldOverride::HIDDEN],
        AddressField::ORGANIZATION => ['override' => FieldOverride::HIDDEN],
      ])
      ->setDisplayOptions('form', [
        'type' => 'readonly_field_widget',
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);
    $fields['call_charge_info'] = BaseFieldDefinition::create('text_long')
      ->setTranslatable(TRUE)
      ->setRevisionable(FALSE)
      ->setLabel(new TranslatableMarkup('Call charge info'))
      ->setDisplayOptions('form', [
        'type' => 'readonly_field_widget',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['address_postal'] = static::createStringField('Address postal');
    $fields['latitude'] = static::createStringField('Latitude')
      ->setTranslatable(FALSE);
    $fields['longitude'] = static::createStringField('Longitude')
      ->setTranslatable(FALSE);
    $fields['streetview_entrance_url'] = static::createLinkField('Streetview entrance')
      ->setTranslatable(FALSE);
    $fields['service_map_embed'] = static::createStringField('Service map embed')
      ->setDisplayOptions('form', [
        'type' => 'readonly_field_widget',
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'service_map_embed',
      ]);
    $fields['services'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Services'))
      ->setSettings([
        'target_type' => 'tpr_service',
      ])
      ->setDisplayOptions('form', [
        'type' => 'readonly_field_widget',
      ])
      ->setRevisionable(FALSE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);
    $fields['menu_link'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Menu link'))
      ->setSettings([
        'target_type' => 'menu_link_content',
      ])
      ->setRevisionable(FALSE)
      ->setTranslatable(TRUE);
    $fields['accessibility_sentences'] = BaseFieldDefinition::create('tpr_accessibility_sentence')
      ->setLabel(new TranslatableMarkup('Accessibility sentences'))
      ->setTranslatable(TRUE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayConfigurable('view', TRUE);
    $fields['provided_languages'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Provided languages'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'readonly_field_widget',
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);
    $fields['hide_description'] = BaseFieldDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Hide description'))
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);
    $fields['show_www'] = BaseFieldDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Show website link'))
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['ontologyword_ids'] = BaseFieldDefinition::create('integer')
      ->setLabel(new TranslatableMarkup('Ontologyword IDs'))
      ->setTranslatable(FALSE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayConfigurable('view', FALSE)
      ->setDisplayConfigurable('form', TRUE)
      ->setSettings([
        'min' => '0',
      ])
      ->setDisplayOptions('form', [
        'region' => 'hidden',
      ]);

    $connectionFields = [
      'links' => [
        'description' => 'LINK',
        'label' => new TranslatableMarkup('Web sites'),
      ],
      'opening_hours' => [
        'description' => 'OPENING_HOURS',
        'label' => new TranslatableMarkup('Opening hours'),
      ],
      'highlights' => [
        'description' => 'HIGHLIGHTS',
        'label' => new TranslatableMarkup('Highlights', [], ['context' => 'TPR Unit field label']),
      ],
      'other_info' => [
        'description' => 'OTHER_INFO',
        'label' => new TranslatableMarkup('Further information'),
      ],
      'price_info' => [
        'description' => 'PRICE',
        'label' => new TranslatableMarkup('Charges'),
      ],
      'contacts' => [
        'description' => 'PHONE_OR_EMAIL',
        'label' => new TranslatableMarkup('Other contact information'),
      ],
      'topical' => [
        'description' => 'TOPICAL',
        'label' => new TranslatableMarkup('Topical', [], ['context' => 'TPR Unit field label']),
      ],
      'subgroup' => [
        'description' => 'SUBGROUP',
        'label' => new TranslatableMarkup('Subgroup', [], ['context' => 'TPR Unit field label']),
      ],
    ];

    foreach ($connectionFields as $name => $data) {
      $fields[$name] = BaseFieldDefinition::create('tpr_connection')
        ->setLabel($data['label'])
        ->setDescription(new TranslatableMarkup('The "@description" connection type', [
          '@description' => $data['description'],
        ]))
        ->setTranslatable(TRUE)
        ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
        ->setDisplayOptions('form', [
          'type' => 'readonly_field_widget',
        ])
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);
    }

    return $fields;
  }

}
