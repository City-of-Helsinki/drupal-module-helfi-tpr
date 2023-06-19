<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Field\Connection;

use Drupal\Component\Utility\Html;
use Drupal\Core\Url;

/**
 * Provides a domain object for TPR connection type of LINK.
 */
final class Link extends Connection {

  /**
   * The type name.
   *
   * @var string
   */
  public const TYPE_NAME = 'LINK';

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
