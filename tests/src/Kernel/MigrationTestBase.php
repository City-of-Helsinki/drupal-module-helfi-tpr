<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\Tests\helfi_api_base\Kernel\MigrationTestBase as ApiMigrationTestBase;
use Drupal\Tests\helfi_tpr\Traits\TprMigrateTrait;

/**
 * Base class to test TPR migrations.
 */
abstract class MigrationTestBase extends ApiMigrationTestBase {

  use TprMigrateTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'link',
    'address',
    'text',
    'helfi_tpr',
    'media',
    'telephone',
    'menu_link_content',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();

    $entity_types = [
      'menu_link_content',
      'tpr_unit',
      'tpr_service',
      'tpr_errand_service',
      'tpr_service_channel',
    ];

    foreach ($entity_types as $type) {
      $this->installEntitySchema($type);
    }
    $this->installConfig(['helfi_tpr']);
  }

}
