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
 *     "canonical" = "/tpr-errand-service/{tpr_errand_service}",
 *     "edit-form" = "/admin/content/integrations/tpr-errand-service/{tpr_errand_service}/edit",
 *     "collection" = "/admin/content/integrations/tpr-errand-service"
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
   * Adds the channel.
   *
   * @param \Drupal\helfi_tpr\Entity\Channel $channel
   *   The channel.
   *
   * @return $this
   *   The self.
   */
  public function addChannel(Channel $channel) : self {
    if (!$this->hasChannel($channel)) {
      $this->get('channels')->appendItem($channel);
    }
    return $this;
  }

  /**
   * Removes the given channel.
   *
   * @param \Drupal\helfi_tpr\Entity\Channel $channel
   *   The channel.
   *
   * @return $this
   *   The self.
   */
  public function removeChannel(Channel $channel) : self {
    $index = $this->getChannelIndex($channel);
    if ($index !== FALSE) {
      $this->get('channels')->offsetUnset($index);
    }
    return $this;
  }

  /**
   * Checks whether the channel exists or not.
   *
   * @param \Drupal\helfi_tpr\Entity\Channel $channel
   *   The errand service.
   *
   * @return bool
   *   Whether we have given channel or not.
   */
  public function hasChannel(Channel $channel) : bool {
    return $this->getChannelIndex($channel) !== FALSE;
  }

  /**
   * Gets the index of the given channel.
   *
   * @param \Drupal\helfi_tpr\Entity\Channel $channel
   *   The channel.
   *
   * @return int|bool
   *   The index of the given channel, or FALSE if not found.
   */
  protected function getChannelIndex(Channel $channel) {
    $values = $this->get('channels')->getValue();
    $ids = array_map(function ($value) {
      return $value['target_id'];
    }, $values);

    return array_search($channel->id(), $ids);
  }

  /**
   * Gets the service channel entities.
   *
   * @return \Drupal\helfi_tpr\Entity\Channel[]
   *   An array of service channel entities.
   */
  public function getChannels() : array {
    /* @phpstan-ignore-next-line */
    return $this->get('channels')->referencedEntities();
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    /** @var \Drupal\Core\Field\BaseFieldDefinition[] $fields */
    $fields = parent::baseFieldDefinitions($entity_type);

    static::$overrideFields['name'] = $fields['name'];

    $fields['type'] = static::createStringField('Type');
    $fields['name_synonyms'] = static::createStringField('Name synonyms', BaseFieldDefinition::CARDINALITY_UNLIMITED);

    $fields['links'] = static::createLinkField('Links')
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED);

    $text_fields = [
      'process_description' => new TranslatableMarkup('Process description'),
      'description' => new TranslatableMarkup('Description'),
      'processing_time' => new TranslatableMarkup('Processing time'),
      'expiration_time' => new TranslatableMarkup('Expiration time'),
      'information' => new TranslatableMarkup('Information'),
      'costs' => new TranslatableMarkup('Costs'),
    ];
    foreach ($text_fields as $name => $label) {
      $fields[$name] = BaseFieldDefinition::create('text_long')
        ->setTranslatable(TRUE)
        ->setLabel((string) $label)
        ->setDisplayOptions('form', [
          'type' => 'readonly_field_widget',
        ])
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);
    }

    $fields['channels'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel((string) new TranslatableMarkup('Channels'))
      ->setSettings([
        'target_type' => 'tpr_service_channel',
      ])
      ->setDisplayOptions('form', [
        'type' => 'readonly_field_widget',
      ])
      ->setTranslatable(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['data'] = BaseFieldDefinition::create('map')
      ->setLabel((string) new TranslatableMarkup('Data'));

    return $fields;
  }

}
