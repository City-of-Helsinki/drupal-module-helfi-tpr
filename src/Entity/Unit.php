<?php

declare(strict_types = 1);

namespace Drupal\helfi_trp\Entity;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\FieldOverride;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the helfi_trp entity class.
 *
 * @ContentEntityType(
 *   id = "trp_unit",
 *   label = @Translation("TRP - Unit"),
 *   label_collection = @Translation("TRP - Unit"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\helfi_trp\Entity\Listing\UnitListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\helfi_api_base\Entity\Access\RemoteEntityAccess",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\helfi_api_base\Entity\Routing\EntityRouteProvider",
 *     }
 *   },
 *   base_table = "trp_unit",
 *   data_table = "trp_unit_field_data",
 *   revision_table = "trp_unit_revision",
 *   revision_data_table = "trp_unit_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer remote entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "langcode" = "langcode",
 *     "uid" = "uid",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   revision_metadata_keys = {
 *     "revision_created" = "revision_timestamp",
 *     "revision_user" = "revision_user",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "canonical" = "/trp-unit/{trp_unit}",
 *     "edit-form" = "/admin/content/trp-unit/{trp_unit}/edit",
 *     "delete-form" = "/admin/content/trp-unit/{trp_unit}/delete",
 *     "collection" = "/admin/content/trp-unit"
 *   },
 *   field_ui_base_route = "trp_unit.settings"
 * )
 */
class Unit extends TrpEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = static::createStringField('Name');
    $fields['latitude'] = static::createStringField('Latitude');
    $fields['longitude'] = static::createStringField('Longitude');
    $fields['phone'] = static::createStringField('Phone', -1);
    $fields['call_charge_info'] = static::createStringField('Call charge info');
    $fields['email'] = static::createStringField('Email');
    $fields['accessibility_phone'] = static::createStringField('Accessibility phone');
    $fields['accessibility_email'] = static::createStringField('Accessibility email');
    $fields['address_postal'] = static::createStringField('Address postal');
    $fields['www'] = static::createLinkField('Website link');
    $fields['streetview_entrance_url'] = static::createLinkField('Streetview entrance');
    $fields['accessibility_www'] = static::createLinkField('Accessibility website link');

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Description'))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['address'] = BaseFieldDefinition::create('address')
      ->setLabel(new TranslatableMarkup('Address'))
      ->setRevisionable(TRUE)
      ->setSetting('field_overrides', [
        AddressField::GIVEN_NAME => ['override' => FieldOverride::HIDDEN],
        AddressField::ADDITIONAL_NAME => ['override' => FieldOverride::HIDDEN],
        AddressField::FAMILY_NAME => ['override' => FieldOverride::HIDDEN],
        AddressField::ORGANIZATION => ['override' => FieldOverride::HIDDEN],
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }

}
