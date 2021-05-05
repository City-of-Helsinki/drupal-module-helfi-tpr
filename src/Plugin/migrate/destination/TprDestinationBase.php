<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\destination;

use Drupal\helfi_api_base\Plugin\migrate\destination\TranslatableEntityBase;
use Drupal\migrate\Row;

/**
 * Shared destination base for tpr entities.
 *
 * Sets default values used on every migrate.
 */
abstract class TprDestinationBase extends TranslatableEntityBase {

  /**
   * Defines default values.
   *
   * @var array
   */
  protected array $defaultValues = [
    'content_translation_uid' => 1,
    'content_translation_status' => FALSE,
  ];

  /**
   * Populates default values.
   *
   * @param \Drupal\migrate\Row $row
   *   The row.
   */
  protected function populateDefaultValues(Row $row) : void {
    // Set default values for entity when we're creating the entity
    // for the first time. These are not supposed to be overriden by
    // the migrate.
    foreach ($this->defaultValues as $key => $value) {
      $row->setDestinationProperty($key, $value);
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function onEntityCreate(string $langcode, Row $row): Row {
    $this->populateDefaultValues($row);
    return parent::onTranslationCreate($langcode, $row);
  }

  /**
   * {@inheritdoc}
   */
  protected function onTranslationCreate(string $langcode, Row $row): Row {
    $this->populateDefaultValues($row);
    return parent::onTranslationCreate($langcode, $row);
  }

}
