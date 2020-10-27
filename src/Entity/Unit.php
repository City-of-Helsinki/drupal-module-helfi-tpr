<?php

declare(strict_types = 1);

namespace Drupal\helfi_trp\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
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

    $string_fields = [
      'name' => ['label' => new TranslatableMarkup('Name')],
      'latitude' => ['label' => new TranslatableMarkup('Latitude')],
      'longitude' => ['label' => new TranslatableMarkup('Longitude')],
      'phone' => [
        'label' => new TranslatableMarkup('Phone'),
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
      ],
      'call_charge_info' => ['label' => new TranslatableMarkup('Call charge info')],
      'email' => ['label' => new TranslatableMarkup('Email')],
      'accessibility_phone' => ['label' => new TranslatableMarkup('Accessibility phone')],
      'accessibility_email' => ['label' => new TranslatableMarkup('Accessibility email')],
    ];

    foreach ($string_fields as $name => $settings) {
      $fields[$name] = BaseFieldDefinition::create('string')
        ->setLabel($settings['label'])
        ->setSettings([
          'max_length' => 255,
          'text_processing' => 0,
        ])
        ->setTranslatable(TRUE)
        ->setDefaultValue('')
        ->setDisplayConfigurable('view', TRUE)
        ->setDisplayConfigurable('form', TRUE);

      if (isset($settings['cardinality'])) {
        $fields[$name]->setCardinality($settings['cardinality']);
      }
    }


    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Description'))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
