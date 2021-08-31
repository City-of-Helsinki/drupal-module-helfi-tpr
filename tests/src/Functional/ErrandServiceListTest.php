<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

/**
 * Tests Errand Service entity's list functionality.
 *
 * @group helfi_tpr
 */
class ErrandServiceListTest extends ListTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();
    $this->listPermissions = [
      'access remote entities overview',
    ];
    $this->adminListPath = '/admin/content/integrations/tpr-errand-service';
  }

}
