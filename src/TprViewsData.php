<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr;

use Drupal\views\EntityViewsData;

/**
 * Provides the views data for TPR entity types.
 */
class TprViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Get data table dynamically since this has to work with all TPR entities.
    $data_table = $this->entityType->getDataTable();
    if ($data_table) {
      $data[$data_table]['status_extra'] = [
        'title' => $this->t('Published status or admin user'),
        'help' => $this->t('Filters out unpublished content if the current user cannot view it.'),
        'filter' => [
          'field' => 'content_translation_status',
          'id' => 'tpr_status',
          'label' => $this->t('Published status or admin user'),
        ],
      ];
    }

    return $data;
  }

}
