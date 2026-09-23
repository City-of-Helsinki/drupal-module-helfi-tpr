<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Entity;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\FieldOverride;
use Drupal\Core\Entity\Attribute\ContentEntityType;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\helfi_api_base\Entity\Access\RemoteEntityAccess;
use Drupal\helfi_api_base\Entity\Routing\EntityRouteProvider;
use Drupal\helfi_tpr\Entity\Listing\ListBuilder;
use Drupal\helfi_tpr\TprViewsData;

/**
 * Defines the tpr_service_channel entity class.
 */
#[ContentEntityType(
  id: 'tpr_service_channel',
  label: new TranslatableMarkup('TPR - Service Channel'),
  label_collection: new TranslatableMarkup('TPR - Service Channel'),
  handlers: [
    'view_builder' => EntityViewBuilder::class,
    'list_builder' => ListBuilder::class,
    'views_data' => TprViewsData::class,
    'access' => RemoteEntityAccess::class,
    'translation' => TranslationHandler::class,
    'form' => [
      'default' => ContentEntityForm::class,
    ],
    'route_provider' => [
      'html' => EntityRouteProvider::class,
    ],
  ],
  base_table: 'tpr_service_channel',
  data_table: 'tpr_service_channel_field_data',
  revision_table: 'tpr_service_channel_revision',
  revision_data_table: 'tpr_service_channel_field_revision',
  show_revision_ui: TRUE,
  translatable: TRUE,
  admin_permission: 'administer remote entities',
  entity_keys: [
    'id' => 'id',
    'revision' => 'revision_id',
    'langcode' => 'langcode',
    'label' => 'name',
    'uuid' => 'uuid',
    'published' => 'content_translation_status',
    'owner' => 'content_translation_uid',
  ],
  revision_metadata_keys: [
    'revision_created' => 'revision_timestamp',
    'revision_user' => 'revision_user',
    'revision_log_message' => 'revision_log',
  ],
  links: [
    'edit-form' => '/admin/content/integrations/tpr-service-channel/{tpr_service_channel}/edit',
    'collection' => '/admin/content/integrations/tpr-service-channel',
  ],
  field_ui_base_route: 'tpr_service_channel.settings',
  additional: [
    'content_translation_ui_skip' => TRUE,
  ],
)]
class Channel extends TprEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function getMigration(): ?string {
    return 'tpr_service_channel';
  }

  /**
   * Gets the type.
   *
   * @return string
   *   The type.
   */
  public function getType() : string {
    return $this->get('type')->value;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name_synonyms'] = static::createStringField('Name synonyms', BaseFieldDefinition::CARDINALITY_UNLIMITED);
    $fields['email'] = static::createEmailField('Email');

    $string_fields = [
      'type' => 'Type',
      'type_string' => 'Type string',
    ];

    foreach ($string_fields as $name => $label) {
      $fields[$name] = static::createStringField($label)
        ->setDisplayOptions('view', [
          'type' => 'string',
          'label' => 'hidden',
        ]);
    }

    $fields['phone'] = static::createPhoneField('Phone');

    $fields['availabilities'] = static::createStringField('Availabilities', BaseFieldDefinition::CARDINALITY_UNLIMITED);

    $fields['address'] = BaseFieldDefinition::create('address')
      ->setLabel(new TranslatableMarkup('Address'))
      ->setTranslatable(TRUE)
      ->setRevisionable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'readonly_field_widget',
      ])
      ->setSetting('field_overrides', [
        AddressField::GIVEN_NAME => ['override' => FieldOverride::HIDDEN],
        AddressField::ADDITIONAL_NAME => ['override' => FieldOverride::HIDDEN],
        AddressField::FAMILY_NAME => ['override' => FieldOverride::HIDDEN],
        AddressField::ORGANIZATION => ['override' => FieldOverride::HIDDEN],
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['links'] = static::createLinkField('Links')
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED);

    $text_fields = [
      'prerequisites' => new TranslatableMarkup('Process description'),
      'availability_summary' => new TranslatableMarkup('Description'),
      'process_description' => new TranslatableMarkup('Processing time'),
      'expiration_time' => new TranslatableMarkup('Expiration time'),
      'authorization_code' => new TranslatableMarkup('Information'),
      'call_charge_info' => new TranslatableMarkup('Call charge info'),
      'information' => new TranslatableMarkup('Additional information'),
    ];
    foreach ($text_fields as $name => $label) {
      $fields[$name] = BaseFieldDefinition::create('text_long')
        ->setTranslatable(TRUE)
        ->setRevisionable(FALSE)
        ->setLabel($label)
        ->setDisplayOptions('form', [
          'type' => 'readonly_field_widget',
        ])
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);
    }

    $boolean_fields = [
      'requires_authentication' => new TranslatableMarkup('Requires authentication'),
      'saved_to_customer_folder' => new TranslatableMarkup('Saved to customer folder'),
      'e_processing' => new TranslatableMarkup('E-processing'),
      'e_decision' => new TranslatableMarkup('E-decision'),
      'payment_enabled' => new TranslatableMarkup('Payment enabled'),
      'for_personal_customer' => new TranslatableMarkup('For personal customer'),
      'for_corporate_customer' => new TranslatableMarkup('For corporate customer'),
    ];

    foreach ($boolean_fields as $name => $label) {
      $fields[$name] = BaseFieldDefinition::create('boolean')
        ->setTranslatable(TRUE)
        ->setRevisionable(FALSE)
        ->setLabel($label)
        ->setDisplayOptions('form', [
          'type' => 'readonly_field_widget',
        ])
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);
    }

    return $fields;
  }

}
