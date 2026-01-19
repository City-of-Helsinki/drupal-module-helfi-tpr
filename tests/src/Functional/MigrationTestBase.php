<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Tests\helfi_api_base\Functional\MigrationTestBase as ApiMigrationTestBase;
use Drupal\Tests\helfi_api_base\Traits\ApiTestTrait;
use Drupal\user\UserInterface;

/**
 * Base class for multilingual migration tests.
 */
abstract class MigrationTestBase extends ApiMigrationTestBase {

  use ApiTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'media',
    'block',
    'readonly_field_widget',
    'media_library',
    'entity',
    'helfi_tpr',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The account.
   *
   * @var \Drupal\user\UserInterface
   */
  protected UserInterface $privilegedAccount;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->drupalPlaceBlock('local_tasks_block');
    $this->privilegedAccount = $this->createUser([
      'administer tpr_unit',
      'administer tpr_service',
      'translate editable entities',
      'view all tpr_unit revisions',
      'view all tpr_service revisions',
      'revert all tpr_unit revisions',
      'revert all tpr_service revisions',
    ]);
  }

}
