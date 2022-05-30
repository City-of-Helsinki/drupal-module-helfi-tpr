<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Field\Connection;

use Drupal\Component\Utility\Html;

/**
 * Provides a domain object for TPR connection type of HIGHLIGHT.
 */
final class Highlight extends Connection {

  /**
   * The type name.
   *
   * @var string
   */
  public const TYPE_NAME = 'HIGHLIGHT';

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
    return [
      'name' => [
        '#markup' => Html::escape($this->get('name')),
      ],
    ];
  }

}
