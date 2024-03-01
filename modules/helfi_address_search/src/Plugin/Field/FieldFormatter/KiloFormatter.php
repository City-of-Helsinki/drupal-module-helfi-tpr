<?php

declare(strict_types=1);

namespace Drupal\helfi_address_search\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Plugin\Field\FieldFormatter\DecimalFormatter;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'number_decimal_kilo' formatter.
 *
 * Shows the number in kilos.
 *
 * @FieldFormatter(
 *   id = "number_decimal_kilo",
 *   label = @Translation("Number in kilos"),
 *   field_types = {
 *     "integer",
 *     "decimal",
 *     "float"
 *   }
 * )
 */
class KiloFormatter extends DecimalFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'thousand_separator' => ' ',
      'decimal_separator' => ',',
      'scale' => 1,
      'prefix_suffix' => TRUE,
      'minimum_value' => 0.1,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['minimum_value'] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum value', [], ['context' => 'decimal places']),
      '#step' => '.01',
      '#min' => 0,
      '#max' => 1000,
      '#default_value' => $this->getSetting('minimum_value'),
      '#description' => $this->t('If the number is smaller than minimum value, minimum value is shown.'),
      '#weight' => 7,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  protected function numberFormat($number): string {
    $number = $number / 1000;
    if (!empty($this->getSetting('minimum_value')) && ($number < $this->getSetting('minimum_value'))) {
      (float) $number = $this->getSetting('minimum_value');
    }
    return number_format($number, $this->getSetting('scale'), $this->getSetting('decimal_separator'), $this->getSetting('thousand_separator'));
  }

}
