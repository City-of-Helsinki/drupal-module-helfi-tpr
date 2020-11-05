<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Entity;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\FieldOverride;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the tpr_organization entity class.
 *
 * @ContentEntityType(
 *   id = "tpr_organization",
 *   label = @Translation("TPR - Organization"),
 *   label_collection = @Translation("TPR - Organization"),
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
 *     }
 *   },
 *   base_table = "tpr_organization",
 *   data_table = "tpr_organization_field_data",
 *   revision_table = "tpr_organization_revision",
 *   revision_data_table = "tpr_organization_field_revision",
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
 *     "canonical" = "/tpr-organization/{tpr_organization}",
 *     "edit-form" = "/admin/content/tpr-organization/{tpr_organization}/edit",
 *     "delete-form" = "/admin/content/tpr-organization/{tpr_organization}/delete",
 *     "collection" = "/admin/content/tpr-organization"
 *   },
 *   field_ui_base_route = "tpr_organization.settings"
 * )
 */
class Organization extends TprEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = static::createStringField('Name');
    $fields['business_id'] = static::createStringField('Business ID')
      ->setTranslatable(FALSE);
    $fields['phone'] = static::createStringField('Phone', -1)
      ->setTranslatable(FALSE);
    $fields['oid'] = static::createStringField('OID')
      ->setTranslatable(FALSE);
    $fields['email'] = static::createLinkField('Email')
      ->setTranslatable(FALSE);
    $fields['address_postal'] = static::createStringField('Address postal');
    $fields['www'] = static::createLinkField('Website link');

    $fields['address'] = BaseFieldDefinition::create('address')
      ->setLabel(new TranslatableMarkup('Address'))
      ->setTranslatable(TRUE)
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
