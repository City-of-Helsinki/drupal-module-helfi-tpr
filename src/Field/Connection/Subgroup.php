<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Field\Connection;

use Drupal\Component\Utility\Html;

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
    $name = '';

    foreach ($fields as $field) {
      if (!$this->get($field)) {
        continue;
      }

      $data = Html::escape($this->get($field));

      if ($field === 'name') {
        $fields_data[] = '<strong>' . $data . '</strong>';
      }
      elseif ($field === 'email') {
        $fields_data[] = '<a href="mailto:' . $data . '" data-is-external="true" data-protocol="mailto">' . $data . '</a>';
      }
      elseif ($field === 'phone') {
        $fields_data[] = '<a href="tel:' . $data . '" data-is-external="true" data-protocol="tel">' . $data . '</a>';
      }
      else {
        $fields_data[] = $data;
      }
    }

    $markup = '<p>' . implode('<br>', $fields_data) . '</p>';

    $build = [
      'contact' => [
        '#markup' => $markup,
      ],
    ];

    return $build;
  }

}
