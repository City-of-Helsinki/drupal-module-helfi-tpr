<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\views\filter;

use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;

/**
 * Base views filter class for ontology word details.
 */
abstract class OntologyWordDetailsBase extends InOperator {

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
    $this->valueTitle = t('Allowed clarifications');
    $this->definition['options callback'] = [$this, 'generateOptions'];
  }

  /**
   * Add relationships to Ontology word details related tables.
   */
  public function query() {
    if (empty($this->value)) {
      return;
    }

    // Join with tpr_ontology_word_details_field_data table.
    $owdFdConfiguration = [
      'table' => 'tpr_ontology_word_details_field_data',
      'field' => 'unit_id',
      'left_table' => 'tpr_unit_field_data',
      'left_field' => 'id',
      'operator' => '=',
    ];
    /** @var \Drupal\views\Plugin\views\join\JoinPluginBase $owdFdJoin */
    $owdFdJoin = Views::pluginManager('join')->createInstance('standard', $owdFdConfiguration);
    $this->query->addRelationship('owd_fd', $owdFdJoin, 'tpr_unit_field_data');

    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $this->query->addWhere('AND', 'owd_fd.langcode', $language);

    // Join with tpr_ontology_word_details__school_details table.
    $owdSdConfiguration = [
      'table' => 'tpr_ontology_word_details__school_details',
      'field' => 'entity_id',
      'left_table' => 'owd_fd',
      'left_field' => 'id',
      'operator' => '=',
    ];
    /** @var \Drupal\views\Plugin\views\join\JoinPluginBase $owdSdJoin */
    $owdSdJoin = Views::pluginManager('join')->createInstance('standard', $owdSdConfiguration);
    $this->query->addRelationship('owd_sd', $owdSdJoin, 'owd_fd');

    $this->query->addWhere('AND', 'owd_sd.school_details_clarification', $this->value);
  }

  /**
   * Generates the options from ontology word details.
   *
   * @return string[]
   *   Available options for the filter.
   */
  abstract public function generateOptions(): array;

}
