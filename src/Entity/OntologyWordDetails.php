<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Entity;

use Drupal\content_translation\ContentTranslationHandler;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the tpr_ontology_word_details entity class.
 *
 * @ContentEntityType(
 *   id = "tpr_ontology_word_details",
 *   label = @Translation("TPR - Ontology word details"),
 *   label_collection = @Translation("TPR - Ontology word details"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\helfi_tpr\Entity\Listing\ListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\entity\EntityAccessControlHandler",
 *     "permission_provider" = "Drupal\entity\EntityPermissionProvider",
 *     "translation" = "Drupal\helfi_tpr\Entity\TranslationHandler",
 *     "form" = {
 *       "default" = "Drupal\helfi_tpr\Entity\Form\ContentEntityForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\helfi_api_base\Entity\Routing\EntityRouteProvider",
 *       "revision" = "\Drupal\helfi_api_base\Entity\Routing\RevisionRouteProvider",
 *     },
 *     "local_action_provider" = {
 *       "collection" = "\Drupal\entity\Menu\EntityCollectionLocalActionProvider",
 *     },
 *     "local_task_provider" = {
 *       "default" = "\Drupal\entity\Menu\DefaultEntityLocalTaskProvider",
 *     },
 *   },
 *   base_table = "tpr_ontology_word_details",
 *   data_table = "tpr_ontology_word_details_field_data",
 *   revision_table = "tpr_ontology_word_details_revision",
 *   revision_data_table = "tpr_ontology_word_details_field_revision",
 *   show_revision_ui = TRUE,
 *   revisionable = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer tpr_ontology_word_details",
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
 *     "canonical" = "/tpr-ontology-word-details/{tpr_ontology_word_details}",
 *     "edit-form" = "/admin/content/integrations/tpr-ontology-word-details/{tpr_ontology_word_details}/edit",
 *     "collection" = "/admin/content/integrations/tpr-ontology-word-details",
 *     "version-history" = "/admin/content/integrations/tpr-ontology-word-details/{tpr_ontology_word_details}/revisions",
 *   },
 *   field_ui_base_route = "tpr_ontology_word_details.settings"
 * )
 */
class OntologyWordDetails extends TprEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function getMigration(): ?string {
    return 'tpr_ontology_word_details';
  }

  /**
   * Loads one or more entities by ontology word ID.
   *
   * @param int $word_id
   *   Ontology word ID.
   *
   * @return array
   *   OntologyWordDetails entities.
   */
  public static function loadMultipleByWordId(int $word_id): array {
    $ids = \Drupal::entityQuery('tpr_ontology_word_details')
      ->condition('content_translation_status', 1)
      ->condition('ontologyword_id', $word_id)
      ->execute();
    return OntologyWordDetails::loadMultiple($ids);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['unit_id'] = static::createStringField('Unit ID')
      ->setTranslatable(FALSE);

    $fields['ontologyword_id'] = static::createStringField('Ontology word ID')
      ->setTranslatable(FALSE);

    $fields['school_details'] = BaseFieldDefinition::create('tpr_school_details')
      ->setLabel(new TranslatableMarkup('School details'))
      ->setRevisionable(FALSE)
      ->setTranslatable(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'readonly_field_widget',
      ]);

    $fields['content_translation_source'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Translation source'))
      ->setDescription(t('The source language from which this translation was created.'))
      ->setDefaultValue(LanguageInterface::LANGCODE_NOT_SPECIFIED)
      ->setInitialValue(LanguageInterface::LANGCODE_NOT_SPECIFIED)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['content_translation_outdated'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Translation outdated'))
      ->setDescription(t('A boolean indicating whether this translation needs to be updated.'))
      ->setDefaultValue(FALSE)
      ->setInitialValue(FALSE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['content_translation_uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Translation author'))
      ->setDescription(t('The author of this translation.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setRevisionable(TRUE)
      ->setDefaultValueCallback(ContentTranslationHandler::class . '::getDefaultOwnerId')
      ->setTranslatable(TRUE);

    $fields['content_translation_status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Translation status'))
      ->setDescription(t('A boolean indicating whether the translation is visible to non-translators.'))
      ->setDefaultValue(TRUE)
      ->setInitialValue(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['content_translation_created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Translation created time'))
      ->setDescription(t('The Unix timestamp when the translation was created.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['content_translation_changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Translation changed time'))
      ->setDescription(t('The Unix timestamp when the translation was most recently saved.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

  /**
   * Gets details filtered by another details using a given field.
   *
   * @param string $fieldName
   *   Name of the entity's field which is used to get the details.
   * @param string $detail
   *   Name of the details that are returned.
   * @param string $filterName
   *   Name of the details that are used to filter the returned details.
   * @param string $filterValue
   *   The filter value that must be matched.
   *
   * @return string[]|null
   *   Array containing the details.
   */
  public function getDetailByAnother(string $fieldName, string $detail, string $filterName, string $filterValue): ?array {
    $data = [];
    if (!$this->get($fieldName)->isEmpty()) {
      foreach ($this->get($fieldName)->getValue() as $item) {
        if ($item[$filterName] = $filterValue) {
          $data[$item[$detail]] = $item[$detail];
        }
      }
    }
    return $data ?? NULL;
  }

}
