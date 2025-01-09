<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Entity;

use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionLogEntityTrait;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Utility\Error;
use Drupal\helfi_api_base\Entity\RemoteEntityBase;
use Drupal\user\EntityOwnerInterface;
use Drupal\user\EntityOwnerTrait;
use GuzzleHttp\Exception\ClientException;

/**
 * Defines the base class for all TPR entities.
 */
abstract class TprEntityBase extends RemoteEntityBase implements RevisionableInterface, RevisionLogInterface, EntityPublishedInterface, EntityOwnerInterface {

  use RevisionLogEntityTrait;
  use BaseFieldTrait;
  use EntityPublishedTrait;
  use EntityOwnerTrait;
  use StringTranslationTrait;
  use LoggerChannelTrait;

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
   *
   * We set max sync attempts to 0 to disable automatic migration
   * cleanup tasks.
   */
  public const MAX_SYNC_ATTEMPTS = 0;

  /**
   * {@inheritdoc}
   */
  public function label() {
    // @todo Fix after core issue has been resolved.
    // https://www.drupal.org/project/drupal/issues/3423205
    // Getting name_override after deletion causes exception
    // because field does not exist when drupal is trying to log the deletion.
    if (!isset($this->translations[$this->activeLangcode]['status'])) {
      return parent::label();
    }

    // Use overridden name field as default label when possible.
    if (!$this->get('name_override')->isEmpty()) {
      return $this->get('name_override')->value;
    }

    return parent::label();
  }

  /**
   * Creates duplicate overridable fields for given base fields.
   *
   * @param \Drupal\Core\Field\BaseFieldDefinition[] $fields
   *   The field definitions.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition[]
   *   The field definitions.
   */
  protected static function createOverrideFields(array $fields) : array {
    // Create duplicate fields that can be modified by end users and
    // are ignored by migrations.
    $weight = -20;
    foreach ($fields as $name => $field) {
      $field->setDisplayOptions('form', [
        'weight' => $weight++,
        'type' => 'readonly_field_widget',
      ]);
      $override_field = clone $field;
      $override_field
        ->setRevisionable(TRUE)
        ->setDisplayConfigurable('view', TRUE)
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayOptions('form', [
          'weight' => $weight++,
        ])
        ->setLabel(
          new TranslatableMarkup('Override: @field_name', [
            '@field_name' => $field->getLabel(),
          ])
        );
      $fields[sprintf('%s_override', $name)] = $override_field;
    }

    return $fields;
  }

  /**
   * Helper function to create a basic link field.
   *
   * @param string $label
   *   The label.
   * @param int $cardinality
   *   The cardinality.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   The field definition.
   */
  protected static function createLinkField(string $label, int $cardinality = 1) : BaseFieldDefinition {
    return static::createBaseField(BaseFieldDefinition::create('link'), $label)
      ->setCardinality($cardinality)
      ->setSettings([
        'max_length' => 255,
      ]);
  }

  /**
   * Gets the author.
   *
   * @return \Drupal\Core\Session\AccountInterface|null
   *   The account.
   *
   * @codingStandardsIgnoreStart
   * @deprecated Use ::getOwner() instead.
   * @codingStandardsIgnoreEnd
   */
  public function getAuthor() : ? AccountInterface {
    return $this->getOwner();
  }

  /**
   * Gets the changed time.
   *
   * @return int|null
   *   The timestamp.
   */
  public function getChangedTime() : ? int {
    $value = $this->get('content_translation_changed')->value;

    return $value ? (int) $value : NULL;
  }

  /**
   * Gets the created time.
   *
   * @return int|null
   *   The timestamp.
   */
  public function getCreatedTime() : ? int {
    $value = $this->get('content_translation_created')->value;

    return $value ? (int) $value : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::revisionLogBaseFieldDefinitions($entity_type);
    $fields += static::ownerBaseFieldDefinitions($entity_type);
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    static::$overrideFields['name'] = static::createStringField('Name')
      ->setDisplayOptions('view', [
        'type' => 'string',
        'label' => 'hidden',
      ]);

    // Add overridable fields as base fields.
    $fields += static::$overrideFields;

    // Create duplicate fields that can be modified by end users and
    // are ignored by migrations.
    $fields += static::createOverrideFields(static::$overrideFields);

    foreach (['changed', 'created'] as $field) {
      // Remove changed and created fields.
      unset($fields[$field]);
    }

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function delete(bool $forceDelete = FALSE): void {
    if (isset($this->getEntityType()->getLinkTemplates()['delete-form'])) {
      \Drupal::messenger()->addWarning(
        $this->t('Entity is not deletable')
      );
    }

    if ($this->helfiTprEntityExists()) {
      \Drupal::messenger()->addWarning(
        $this->t('Cannot delete TPR-entity which still exists in the API')
      );
      return;
    }

    parent::delete($forceDelete);
  }

  /**
   * Check if the entity being deleted exists in the API.
   *
   * @return bool
   *   The entity exists in api.
   */
  private function helfiTprEntityExists() : bool {
    $entityTypeId = $this->getEntityTypeId();
    /** @var \Drupal\migrate\Plugin\MigrationPluginManager $migrationPluginManager */
    $migrationPluginManager = \Drupal::service('plugin.manager.migration');

    $urlMap = [
      'tpr_errand_service' => 'canonical_url',
      'tpr_ontology_word_details' => 'details_url',
      'tpr_service' => 'canonical_url',
      'tpr_unit' => 'url',
    ];
    $key = $urlMap[$entityTypeId];
    $url = $migrationPluginManager->getDefinition($entityTypeId)['source'][$key];

    $request_url = sprintf(
      '%s/%s%s',
      rtrim(strtok($url, '?'), '/'),
      $this->id(),
      "?language={$this->language()->getId()}"
    );

    try {
      $response = \Drupal::httpClient()
        ->request('GET', $request_url);
      $data = $response->getBody()
        ->getContents();
    }
    catch (ClientException $e) {
      if ($e->getResponse()->getStatusCode() === 404) {
        return FALSE;
      }

      Error::logException($this->getLogger('helfi_tpr'), $e);
      // Prevent from deleting.
      return TRUE;
    }

    if (!json_validate($data) || $data === 'null') {
      return FALSE;
    }

    $api_data = json_decode($data, TRUE);
    if (isset($api_data['id']) && $api_data['id'] == $this->id()) {
      return TRUE;
    }

    return FALSE;
  }

}
