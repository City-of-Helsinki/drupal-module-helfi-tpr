<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Field\Connection;

use Drupal\Component\Utility\Html;
use Drupal\Core\Url;

/**
 * A base class for connections with text and link.
 */
abstract class TextWithLinkBase extends Connection {

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

    if (function_exists('_filter_autop')) {
      $markup = _filter_autop($markup);
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
