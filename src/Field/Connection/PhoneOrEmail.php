<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Field\Connection;

use Drupal\Component\Utility\Html;

/**
 * Provides a domain object for TPR connection type of PHONE_OR_EMAIL.
 */
final class PhoneOrEmail extends Connection {

  /**
   * The type name.
   *
   * @var string
   */
  public const TYPE_NAME = 'PHONE_OR_EMAIL';

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

      $fields_data[] = $data;
    }

    $build = [
      'contact' => [
        '#markup' => '<p>' . implode('<br>', $fields_data) . '</p>',
      ],
    ];

    return $build;
  }

}
