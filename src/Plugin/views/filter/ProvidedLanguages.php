<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Plugin\views\filter;

use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\ViewExecutable;

/**
 * Filter units by provided language.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("tpr_provided_languages")
 */
class ProvidedLanguages extends InOperator {

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, ?array &$options = NULL): void {
    parent::init($view, $display, $options);
    $this->valueTitle = (string) $this->t('Allowed languages');
    $this->definition['options callback'] = [$this, 'generateOptions'];
  }

  /**
   * Generates options.
   *
   * @return string[]
   *   Available options for the filter.
   */
  public function generateOptions(): array {
    return [
      'fi' => (string) $this->t('Finnish'),
      'sv' => (string) $this->t('Swedish'),
      'se' => (string) $this->t('North Sami'),
    ];
  }

}
