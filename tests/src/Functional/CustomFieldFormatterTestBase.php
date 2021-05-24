<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\helfi_tpr\Entity\TprEntityBase;
use Drupal\helfi_tpr\Entity\Unit;
use Drupal\Tests\helfi_api_base\Functional\MigrationTestBase;

/**
 * Base class to test TPR field formatters.
 *
 * @group helfi_tpr
 */
abstract class CustomFieldFormatterTestBase extends MigrationTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field',
    'helfi_tpr',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The entity to test.
   *
   * @var \Drupal\helfi_tpr\Entity\TprEntityBase
   */
  protected TprEntityBase $entity;

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();

    $this->entity = $this->getEntity();

    $account = $this->createUser($this->getUserPermissions());
    $this->drupalLogin($account);

  }

  /**
   * Gets the user permissions.
   *
   * @return string[]
   *   The permissions.
   */
  protected function getUserPermissions() : array {
    return [
      'view ' . $this->entity->getEntityTypeId(),
    ];
  }

  /**
   * Gets the entity used in test.
   *
   * @return \Drupal\helfi_tpr\Entity\TprEntityBase
   *   The entity.
   */
  protected function getEntity() : TprEntityBase {
    $entity = Unit::create([
      'id' => 999,
      'langcode' => 'fi',
      'name' => 'Test',
    ]);

    $entity->addTranslation('sv', ['name' => 'Test'])
      ->addTranslation('en', ['name' => 'Test'])
      ->save();

    return $entity;
  }

}
