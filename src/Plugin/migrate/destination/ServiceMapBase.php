<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\destination;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Row;

/**
 * Provides a destination plugin for Tpr entities.
 */
abstract class ServiceMapBase extends TranslatableEntityBase {

  /**
   * {@inheritdoc}
   */
  protected function getEntity(Row $row, array $old_destination_id_values) {
    $default_language = $this->languageManager->getDefaultLanguage();
    $row = $this->populateFieldTranslations($default_language, $row);
    /** @var \Drupal\helfi_tpr\Entity\Unit $entity */
    $entity = parent::getEntity($row, $old_destination_id_values);

    $languages = $this->languageManager->getLanguages();
    unset($languages[$default_language->getId()]);

    foreach ($this->languageManager->getLanguages() as $langcode => $language) {
      $languageRow = $this->populateFieldTranslations($language, $row);

      if ($entity->hasTranslation($langcode)) {
        // Update existing translation.
        $translation = $entity->getTranslation($langcode);
        $this->updateEntity($translation, $languageRow);
      }
      else {
        // Stubs might need some required fields filled in.
        if ($languageRow->isStub()) {
          $this->processStubRow($languageRow);
        }
        $translation = $entity->addTranslation($langcode, $languageRow->getDestination());
        $translation->enforceIsNew();
      }
    }
    return $entity;
  }

  /**
   * Gets the translatable source fields.
   *
   * Defined as remote field name => local field name:
   *
   * @code
   * [
   *   'name => 'field_name',
   *   'www' =>  'field_url',
   * ]
   * @endcode
   * Language code will be appended to remote field automatically. For
   * example the field `name` will become name_fi, name_en etc.
   *
   * @return string[]
   *   An array of source fields.
   */
  abstract protected function getTranslatableFields() : array;

  /**
   * Populates the row object values.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language.
   * @param \Drupal\migrate\Row $row
   *   The row.
   *
   * @return \Drupal\migrate\Row
   *   The row.
   */
  protected function populateFieldTranslations(LanguageInterface $language, Row $row) : Row {
    $langcode = $language->getId();

    if (!$row->get('langcode')) {
      $row->setDestinationProperty('langcode', $langcode);
    }

    foreach ($this->getTranslatableFields() as $remote => $local) {
      $field = sprintf('%s_%s', $remote, $langcode);

      // Attempt to read source property in current language and fallback to
      // finnish.
      $value = $row->hasSourceProperty($field) ? $row->getSourceProperty($field) : $row->getSourceProperty(sprintf('%s_fi', $remote));

      if (!$value) {
        continue;
      }
      $row->setDestinationProperty($local, $value);
    }

    return $row;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return ['id' => ['type' => 'string']];
  }

}
