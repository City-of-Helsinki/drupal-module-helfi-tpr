<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Field\Connection;

use Drupal\Component\Utility\Html;
use Drupal\Core\Language\LanguageInterface;
use Drupal\filter\Plugin\Filter\FilterAutoP;

/**
 * Provides a domain object for TPR connection type of PRICE.
 */
final class Price extends Connection {

  /**
   * The type name.
   *
   * @var string
   */
  public const TYPE_NAME = 'PRICE';

  /**
   * {@inheritdoc}
   */
  public function getFields(): array {
    return [
      'name',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $markup = Html::escape($this->get('name'));

    if (class_exists(FilterAutoP::class)) {
      $markup = (new FilterAutoP([], 'filter_autop', ['provider' => 'filter']))
        ->process($markup, LanguageInterface::LANGCODE_NOT_SPECIFIED)
        ->getProcessedText();
    }

    return [
      'name' => [
        '#markup' => $markup,
      ],
    ];
  }

}
