<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Field\Connection;

use Drupal\Component\Utility\Html;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Url;
use Drupal\filter\Plugin\Filter\FilterAutoP;

/**
 * A base class for connections with text and link.
 */
abstract class TextWithLink extends Connection {

  /**
   * {@inheritdoc}
   */
  public function getFields(): array {
    return [
      'name',
      'www',
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

    $build = [
      'name' => [
        '#markup' => $markup,
      ],
    ];

    if ($link = $this->get('www')) {
      try {
        return [
          'www' => [
            '#title' => $this->get('name'),
            '#type' => 'link',
            '#url' => Url::fromUri($link),
          ],
        ];
      }
      catch (\InvalidArgumentException) {
      }
    }

    return $build;
  }

}
