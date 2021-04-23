<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the tpr_service entity class.
 *
 * @ContentEntityType(
 *   id = "tpr_service",
 *   label = @Translation("TPR - Service"),
 *   label_collection = @Translation("TPR - Service"),
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
 *   base_table = "tpr_service",
 *   data_table = "tpr_service_field_data",
 *   revision_table = "tpr_service_revision",
 *   revision_data_table = "tpr_service_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer remote entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "langcode" = "langcode",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   revision_metadata_keys = {
 *     "revision_created" = "revision_timestamp",
 *     "revision_user" = "revision_user",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "canonical" = "/tpr-service/{tpr_service}",
 *     "edit-form" = "/admin/content/integrations/tpr-service/{tpr_service}/edit",
 *     "delete-form" = "/admin/content/integrations/tpr-service/{tpr_service}/delete",
 *     "collection" = "/admin/content/integrations/tpr-service"
 *   },
 *   field_ui_base_route = "tpr_service.settings"
 * )
 */
class Service extends TprEntityBase {

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
  public static function getMigration(): ?string {
    return 'tpr_service';
  }

  /**
   * Gets the data.
   *
   * @param string $key
   *   The key.
   * @param null|mixed $default
   *   The default value.
   *
   * @return mixed|null
   *   The data.
   */
  public function getData(string $key, $default = NULL) {
    $data = [];
    if (!$this->get('data')->isEmpty()) {
      $data = $this->get('data')->first()->getValue();
    }
    return isset($data[$key]) ? $data[$key] : $default;
  }

  /**
   * Sets the data.
   *
   * @param string $key
   *   The key.
   * @param mixed $value
   *   The value.
   *
   * @return $this
   *   The self.
   */
  public function setData(string $key, $value) : self {
    $this->get('data')->__set($key, $value);
    return $this;
  }

  /**
   * Adds the given errand service.
   *
   * @param \Drupal\helfi_tpr\Entity\ErrandService $errand_service
   *   The errand service.
   *
   * @return $this
   *   The self.
   */
  public function addErrandService(ErrandService $errand_service) : self {
    if (!$this->hasErrandService($errand_service)) {
      $this->get('errand_services')->appendItem($errand_service);
    }
    return $this;
  }

  /**
   * Removes the given errand_service.
   *
   * @param \Drupal\helfi_tpr\Entity\ErrandService $errand_service
   *   The errand service.
   *
   * @return $this
   *   The self.
   */
  public function removeErrandService(ErrandService $errand_service) : self {
    $index = $this->getErrandServiceIndex($errand_service);
    if ($index !== FALSE) {
      $this->get('errand_services')->offsetUnset($index);
    }
    return $this;
  }

  /**
   * Checks whether the errand service exists or not.
   *
   * @param \Drupal\helfi_tpr\Entity\ErrandService $errand_service
   *   The errand service.
   *
   * @return bool
   *   Whether we have given errand service or not.
   */
  public function hasErrandService(ErrandService $errand_service) : bool {
    return $this->getErrandServiceIndex($errand_service) !== FALSE;
  }

  /**
   * Gets the index of the given errand service.
   *
   * @param \Drupal\helfi_tpr\Entity\ErrandService $errand_service
   *   The errand service.
   *
   * @return int|bool
   *   The index of the given errand service, or FALSE if not found.
   */
  protected function getErrandServiceIndex(ErrandService $errand_service) {
    $values = $this->get('errand_services')->getValue();
    $ids = array_map(function ($value) {
      return $value['target_id'];
    }, $values);

    return array_search($errand_service->id(), $ids);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    static::$overrideFields['name'] = $fields['name'];
    static::$overrideFields['description'] = BaseFieldDefinition::create('text_with_summary')
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Description'))
      ->setDisplayOptions('form', [
        'type' => 'readonly_field_widget',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    static::$overrideFields['links'] = static::createLinkField('Links')
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED);

    // Add overridable fields as base fields.
    $fields += static::$overrideFields;

    // Create duplicate fields that can be modified by end users and
    // are ignored by migrations.
    foreach (static::$overrideFields as $name => $field) {
      $fields[sprintf('%s_ovr', $name)] = clone $field;
    }

    $fields['data'] = BaseFieldDefinition::create('map')
      ->setLabel(new TranslatableMarkup('Data'))
      ->setDescription(new TranslatableMarkup('A serialized array of additional data.'));

    $fields['errand_services'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Errand Services'))
      ->setSettings([
        'target_type' => 'tpr_errand_service',
      ])
      ->setDisplayOptions('form', [
        'type' => 'readonly_field_widget',
      ])
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }

}
