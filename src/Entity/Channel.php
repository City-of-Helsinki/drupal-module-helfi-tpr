<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Entity;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\FieldOverride;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the tpr_service_channel entity class.
 *
 * @ContentEntityType(
 *   id = "tpr_service_channel",
 *   label = @Translation("TPR - Service Channel"),
 *   label_collection = @Translation("TPR - Service Channel"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\helfi_tpr\Entity\Listing\ListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\helfi_api_base\Entity\Access\RemoteEntityAccess",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\helfi_api_base\Entity\Routing\EntityRouteProvider",
 *     },
 *   },
 *   base_table = "tpr_service_channel",
 *   data_table = "tpr_service_channel_field_data",
 *   revision_table = "tpr_service_channel_revision",
 *   revision_data_table = "tpr_service_channel_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer remote entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "langcode" = "langcode",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "published" = "content_translation_status",
 *   },
 *   revision_metadata_keys = {
 *     "revision_created" = "revision_timestamp",
 *     "revision_user" = "revision_user",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "canonical" = "/tpr-service_channel/{tpr_service_channel}",
 *     "edit-form" = "/admin/content/integrations/tpr-service-channel/{tpr_service_channel}/edit",
 *     "delete-form" = "/admin/content/integrations/tpr-service-channel/{tpr_service_channel}/delete",
 *     "collection" = "/admin/content/integrations/tpr-service-channel"
 *   },
 *   field_ui_base_route = "tpr_service_channel.settings"
 * )
 */
class Channel extends TprEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function getMigration(): ?string {
    return 'tpr_service_channel';
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name_synonyms'] = static::createStringField('Name synonyms', BaseFieldDefinition::CARDINALITY_UNLIMITED);

    $fields['email'] = static::createStringField('Email');

    $string_fields = [
      'type' => 'Type',
      'type_string' => 'Type string',
      'email' => 'Email',
    ];

    foreach ($string_fields as $name => $label) {
      $fields[$name] = static::createStringField($label);
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
