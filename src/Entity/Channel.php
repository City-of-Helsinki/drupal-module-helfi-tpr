<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Entity;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\FieldOverride;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

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
 *     "views_data" = "Drupal\helfi_tpr\TprViewsData",
 *     "access" = "Drupal\helfi_api_base\Entity\Access\RemoteEntityAccess",
 *     "translation" = "Drupal\helfi_tpr\Entity\TranslationHandler",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\helfi_api_base\Entity\Routing\EntityRouteProvider",
 *     },
 *   },
 *   content_translation_ui_skip = TRUE,
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
 *     "owner" = "content_translation_uid",
 *   },
 *   revision_metadata_keys = {
 *     "revision_created" = "revision_timestamp",
 *     "revision_user" = "revision_user",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "edit-form" = "/admin/content/integrations/tpr-service-channel/{tpr_service_channel}/edit",
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
    $fields['email'] = static::createStringField('Email');

    $string_fields = [
      'type' => 'Type',
      'type_string' => 'Type string',
      'email' => 'Email',
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

  /**
   * Get mailto link for service channel email address.
   *
   * @return Url|null
   *   Email as mailto link or null.
   */
  public function getMailto():?Url {
    return $this->get('email')->isEmpty() ? NULL : Url::fromUri('mailto:' . $this->get('email')->value);
  }

  /**
   * Get tel link for service channel phone number.
   *
   * @return Url|null
   *   Phone number as tel link or null.
   */
  public function getPhoneNumber():?Url {
    if ($this->get('phone')->isEmpty()) {
      return NULL;
    }

    $trimmed_phone_number = trim($this->get('phone')->value);
    return Url::fromUri("tel:$trimmed_phone_number");
  }

  /**
   * Get render array for service channel address.
   *
   * @return array|null
   *   Render array or null.
   */
  public function getFormattedAddress():?array {
    if ($this->get('address')->isEmpty()) {
      return NULL;
    }

    return $this->get('address')->view('default');
  }

  /**
   * Get default weights for each service channel type.
   *
   * @param string $channel_type
   *   Name of the channel type.
   *
   * @return int|null
   *   Weight for given channel or null.
   */
  public function getChannelWeight(string $channel_type):?int {
    $weight = [
      'ESERVICE' => 0,
      'TELEPHONE' => 1,
      'EMAIL' => 2,
      'PRINTABLE_FORM' => 3,
      'MAIL' => 4,
      'LOCAL' => 5,
      'CHAT' => 6,
      'WEBPAGE' => 7,
      'SMS' => 8,
    ];

    return $weight[$channel_type] ?? NULL;
  }
}
