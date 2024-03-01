<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Field\Connection;

use Drupal\Component\Utility\Html;

/**
 * Provides a domain object for TPR connection type of OPENING_HOUR.
 */
final class OtherInfo extends Connection {

  /**
   * The type name.
   *
   * @var string
   */
  public const TYPE_NAME = 'OTHER_INFO';

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

    if (function_exists('_filter_autop')) {
      $markup = _filter_autop($markup);
    }

    return [
      'name' => [
        '#markup' => $markup,
      ],
    ];
  }

}
