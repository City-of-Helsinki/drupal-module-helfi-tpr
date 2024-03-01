<?php

declare(strict_types=1);

namespace Drupal\helfi_address_search\Plugin\views\area;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\area\AreaPluginBase;

/**
 * Views area address_search_info handler.
 *
 * @ingroup views_area_handlers
 *
 * @ViewsArea("address_search_info")
 */
class AddressSearchInfo extends AreaPluginBase {

  /**
   * Get address search success status from the view.
   *
   * @return bool|null
   *   TRUE: the search was successful
   *   FALSE: address was not found
   *   NULL: the search was not performed.
   */
  protected function getSearchStatus(): ?bool {
    if (isset($this->view->element['#address_search_succeed'])) {
      return $this->view->element['#address_search_succeed'];
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions(): array {
    $options = parent::defineOptions();
    $options['succeed'] = [
      'contains' => [
        'value' => ['default' => ''],
        'format' => ['default' => NULL],
      ],
    ];
    $options['failed'] = [
      'contains' => [
        'value' => ['default' => ''],
        'format' => ['default' => NULL],
      ],
    ];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state): void {
    parent::buildOptionsForm($form, $form_state);

    $form['succeed'] = [
      '#title' => $this->t('Informative text for successful address search'),
      '#type' => 'text_format',
      '#default_value' => $this->options['succeed']['value'],
      '#rows' => 6,
      '#format' => $this->options['succeed']['format'] ?? filter_default_format(),
      '#editor' => FALSE,
    ];

    $form['failed'] = [
      '#title' => $this->t('Informative text for failed address search'),
      '#type' => 'text_format',
      '#default_value' => $this->options['failed']['value'],
      '#rows' => 6,
      '#format' => $this->options['failed']['format'] ?? filter_default_format(),
      '#editor' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render($empty = FALSE): array {
    if ($this->getSearchStatus() === NULL) {
      return [];
    }

    $classes = ['unit-search__additional-information'];
    if ($this->getSearchStatus() === TRUE) {
      $classes[] = 'unit-search__address-found';
      $text = $this->options['succeed']['value'];
      $format = $this->options['succeed']['format'] ?? filter_default_format();
    }
    else {
      $classes[] = 'unit-search__address-not-found';
      $classes[] = 'hds-notification';
      $classes[] = 'hds-notification--alert';
      $text = $this->options['failed']['value'];
      $format = $this->options['failed']['format'] ?? filter_default_format();
    }

    return [
      '#theme' => 'container',
      '#attributes' => [
        'class' => $classes,
      ],
      '#children' => [
        '#type' => 'processed_text',
        '#text' => $text,
        '#format' => $format,
      ],
    ];
  }

}
