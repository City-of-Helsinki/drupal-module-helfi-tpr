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

    // Filter by TPR Unit's provided_languages field.
    $data['tpr_unit__provided_languages']['tpr_provided_languages'] = [
      'title' => $this->t('Provided languages'),
      'filter' => [
        'id' => 'tpr_provided_languages',
        'field' => 'provided_languages_value',
        'label' => $this->t('Provided languages'),
        'help' => 'Filter units by provided languages.',
      ],
    ];

    // Add relationship between Unit and Ontology word details.
    $data['tpr_unit']['tpr_ontology_word_details'] = [
      'title' => t('Ontology word details field data'),
      'relationship' => [
        'base' => 'tpr_ontology_word_details_field_data',
        'base field' => 'unit_id',
        'table' => 'tpr_unit',
        'real field' => 'id',
        'id' => 'tag_owd_relationship',
        'label' => t('Ontology word details field data'),
      ],
    ];

    return $data;
  }

}
