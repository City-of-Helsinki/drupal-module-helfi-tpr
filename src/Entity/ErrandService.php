<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the tpr_errand_service entity class.
 *
 * @ContentEntityType(
 *   id = "tpr_errand_service",
 *   label = @Translation("TPR - Errand Service"),
 *   label_collection = @Translation("TPR - Errand Service"),
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
 *   base_table = "tpr_errand_service",
 *   data_table = "tpr_errand_service_field_data",
 *   revision_table = "tpr_errand_service_revision",
 *   revision_data_table = "tpr_errand_service_field_revision",
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
 *     "canonical" = "/tpr-errand_service/{tpr_errand_service}",
 *     "edit-form" = "/admin/content/integrations/tpr-errand_service/{tpr_errand_service}/edit",
 *     "delete-form" = "/admin/content/integrations/tpr-errand_service/{tpr_errand_service}/delete",
 *     "collection" = "/admin/content/integrations/tpr-errand_service"
 *   },
 *   field_ui_base_route = "tpr_errand_service.settings"
 * )
 */
class ErrandService extends TprEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function getMigration(): ?string {
    return 'tpr_errand_service';
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['type'] = static::createStringField('Type');
    $fields['name_synonyms'] = static::createStringField('Name synonyms', BaseFieldDefinition::CARDINALITY_UNLIMITED);

    $fields['channels'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Channels'))
      ->setSettings([
        'target_type' => 'tpr_service_channel',
        'handler_settings' => [
          'target_bundles' => ['tpr_service_channel'],
        ],
      ])
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $text_fields = [
      'process_description' => new TranslatableMarkup('Process description'),
      'description' => new TranslatableMarkup('Description'),
      'processing_time' => new TranslatableMarkup('Processing time'),
      'expiration_time' => new TranslatableMarkup('Expiration time'),
      'information' => new TranslatableMarkup('Information'),
    ];
    foreach ($text_fields as $name => $label) {
      $fields[$name] = BaseFieldDefinition::create('text')
        ->setTranslatable(TRUE)
        ->setLabel($label)
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);
    }

    return $fields;
  }

}
