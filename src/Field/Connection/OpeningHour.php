<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Field\Connection;

use Drupal\Component\Utility\Html;
use Drupal\Core\Url;

/**
 * Provides a domain object for TPR connection type of OPENING_HOURS.
 */
final class OpeningHour extends Connection {

  /**
   * The type name.
   *
   * @var string
   */
  public const TYPE_NAME = 'OPENING_HOURS';

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
    $build = [
      'name' => [
        '#markup' => Html::escape($this->get('name')),
      ],
    ];

    if ($link = $this->get('www')) {
      return [
        'www' => [
          '#title' => $this->get('name'),
          '#type' => 'link',
          '#url' => Url::fromUri($link),
        ],
      ];
    }

    return $build;
  }

}
