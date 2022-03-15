<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Tests\helfi_tpr\Traits\TprMigrateTrait;

/**
 * Tests Service Channel entity's list functionality.
 *
 * @group helfi_tpr
 */
class ServiceChannelListTest extends ListTestBase {

  use TprMigrateTrait;

  /**
   * {@inheritdoc}
   */
  protected string $entityType = 'tpr_service_channel';

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();
    $this->listPermissions = [
      'access remote entities overview',
      'edit remote entities',
    ];
    $this->adminListPath = '/admin/content/integrations/tpr-service-channel';
  }

  /**
   * Tests list view permissions, and viewing, updating, and publishing.
   */
  public function testList() : void {
    $this->assertListPermissions();
    $this->runServiceChannelMigration();
    // Make sure we can use actions to publish and unpublish content.
    $this->assertPublishAction();
  }

}
