<?php

declare(strict_types = 1);

namespace Drupal\helfi_school_addons\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\helfi_school_addons\SchoolUtility;

/**
 * Change school specific settings, e.g. set the active school year.
 */
class SchoolSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'helfi_school_addons.school_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $currentSchoolYear = SchoolUtility::getCurrentSchoolYear();

    $form['current_school_year_info'] = [
      '#markup' => '<p>' . t('Current school year:') . ' ' . ($currentSchoolYear ? $currentSchoolYear : '-') . '</p>',
    ];

    $form['school_year_first'] = [
      '#type' => 'number',
      '#title' => $this->t('Starting year for school year'),
      '#min' => 2020,
      '#max' => 2200,
      '#default_value' => ($currentSchoolYear ? $this->splitStartYear($currentSchoolYear) : ''),
      '#description' => t('Select the starting year for a school year period. For example, selecting "2022" would set the school year to "2022-2023".'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    SchoolUtility::setCurrentSchoolYear($this->composeSchoolYear((int) $form_state->getValue('school_year_first')));
  }

  /**
   * Gets the start year from a school year period.
   *
   * @param string $schoolYear
   *   The school year, e.g. '2022-2023'.
   *
   * @return string
   *   The year.
   */
  private function splitStartYear(string $schoolYear): string {
    return strtok($schoolYear, '-');
  }

  /**
   * Gets the school year from a starting year.
   *
   * @param int $firstYear
   *   The year.
   *
   * @return string
   *   The school year, e.g. '2022-2023'.
   */
  private function composeSchoolYear(int $firstYear): string {
    return $firstYear . '-' . strval($firstYear + 1);
  }

}
