<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Field\Connection;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Component\Utility\Html;
use Drupal\Core\Url;

/**
 * Provides a domain object for TPR connection type of Subgroup.
 */
final class Subgroup extends Connection {

  /**
   * The type name.
   *
   * @var string
   */
  public const TYPE_NAME = 'SUBGROUP';

  /**
   * {@inheritdoc}
   */
  public function getFields(): array {
    return [
      'name',
      'contact_person',
      'phone',
      'email',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $fields = $this->getFields();
    $fields_data = [];

    foreach ($fields as $field) {
      if (!$this->get($field)) {
        continue;
      }

      $data = Html::escape($this->get($field));

      $fields_data[] = match ($field) {
        'name' => [
          '#markup' => '<strong>' . $data . '</strong>',
        ],
        'contact_person' => [
          '#markup' => '<span>' . $data . '</span>',
          '#prefix' => '<br />',
        ],
        'email' => [
          '#url' => Url::fromUri('mailto:' . $data),
          '#title' => new FormattableMarkup($data, []),
          '#type' => 'link',
          '#prefix' => '<br />',
        ],
        'phone' => [
          '#url' => Url::fromUri('tel:' . $data),
          '#title' => new FormattableMarkup($data, []),
          '#type' => 'link',
          '#prefix' => '<br />',
        ],
      };
    }

    $build = [
      'subgroup' => $fields_data,
    ];

    return $build;
  }

}
