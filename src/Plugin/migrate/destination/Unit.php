<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\destination;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a destination plugin for Tpr entities.
 *
 * @MigrateDestination(
 *   id = "tpr_unit",
 * )
 */
final class Unit extends Tpr {

  /**
   * The language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected LanguageManagerInterface $languageManager;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MigrationInterface $migration = NULL
  ) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition, $migration);
    $instance->languageManager = $container->get('language_manager');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  protected static function getEntityTypeId($plugin_id) {
    return 'tpr_unit';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntity(Row $row, array $old_destination_id_values) {
    $default_language = $this->languageManager->getDefaultLanguage();
    $row = $this->populateRowFields($default_language, $row);
    /** @var \Drupal\helfi_tpr\Entity\Unit $entity */
    $entity = parent::getEntity($row, $old_destination_id_values);

    $languages = $this->languageManager->getLanguages();
    unset($languages[$default_language->getId()]);

    foreach ($this->languageManager->getLanguages() as $langcode => $language) {
      $languageRow = $this->populateRowFields($language, $row);

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

  protected function populateRowFields(LanguageInterface $language, Row $row) : Row {
    $langcode = $language->getId();

    if (!$row->get('langcode')) {
      $row->setDestinationProperty('langcode', $langcode);
    }

    foreach (['name'] as $name) {
      $field = sprintf('%s_%s', $name, $langcode);

      // Attempt to read source property in current language and fallback to
      // finnish.
      $value = $row->hasSourceProperty($field) ? $row->getSourceProperty($field) : $row->getSourceProperty(sprintf('%s_fi', $name));
      $row->setDestinationProperty($name, $value);
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
