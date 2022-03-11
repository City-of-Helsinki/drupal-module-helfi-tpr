<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

/**
 * Tests Service Channel entity's list functionality.
 *
 * @group helfi_tpr
 */
class ServiceChannelListTest extends ListTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();
    $this->listPermissions = [
      'access remote entities overview',
    ];
    $this->adminListPath = '/admin/content/integrations/tpr-service-channel';
  }

  /**
   * Tests list view permissions, and viewing, updating, and publishing units.
   */
  public function testList() : void {
    $this->assertListPermissions();
  }

}
