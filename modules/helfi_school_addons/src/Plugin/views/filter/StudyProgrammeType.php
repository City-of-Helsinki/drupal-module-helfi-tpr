<?php

declare(strict_types = 1);

namespace Drupal\helfi_school_addons\Plugin\views\filter;

use Drupal\helfi_school_addons\SchoolUtility;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;

/**
 * Filter school units by study programme type.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("study_programme_type_filter")
 */
class StudyProgrammeType extends InOperator {

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
    $this->valueTitle = t('Allowed values');
    $this->definition['options callback'] = [$this, 'generateOptions'];
  }

  /**
   * Builds the views filter query.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function query() {
    if (empty($this->value) || (isset($this->value[0]) && $this->value[0] === 'All')) {
      return;
    }

    $valueToWordId = [
      'general' => [
        816,
      ],
      'adult' => [
        650,
        590,
      ],
    ];

    if (!isset($this->value[0]) || !array_key_exists($this->value[0], $valueToWordId)) {
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
    $this->query->addRelationship('owd_fd_spt', $owdFdJoin, 'tpr_unit_field_data');

    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $this->query->addWhere('AND', 'owd_fd_spt.langcode', $language);

    // Join with tpr_ontology_word_details__school_details table.
    $owdSdConfiguration = [
      'table' => 'tpr_ontology_word_details__school_details',
      'field' => 'entity_id',
      'left_table' => 'owd_fd_spt',
      'left_field' => 'id',
      'operator' => '=',
    ];
    /** @var \Drupal\views\Plugin\views\join\JoinPluginBase $owdSdJoin */
    $owdSdJoin = Views::pluginManager('join')->createInstance('standard', $owdSdConfiguration);
    $this->query->addRelationship('owd_sd_spt', $owdSdJoin, 'owd_fd_spt');

    $schoolYear = SchoolUtility::getCurrentSchoolYear();
    if ($schoolYear) {
      $this->query->addWhere('AND', 'owd_sd_spt.school_details_schoolyear', $schoolYear);
    }

    $orGroup = $this->query->setWhereGroup('OR', 'OR');
    foreach ($valueToWordId[$this->value[0]] as $wordId) {
      $this->query->addWhere($orGroup, 'owd_fd_spt.ontologyword_id', $wordId);
    }
  }

  /**
   * Generates options for the filter.
   *
   * @return string[]
   *   Available options for the filter.
   */
  protected function generateOptions(): array {
    return [
      'general' => t('The general programme or study programme'),
      'adult' => t('The upper secondary school for adults or a study programme'),
    ];
  }

}
