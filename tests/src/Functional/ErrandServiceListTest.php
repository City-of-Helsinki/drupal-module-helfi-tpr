<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Tests\helfi_api_base\Functional\MigrationTestBase;
use Drupal\Tests\helfi_api_base\Traits\ApiTestTrait;

/**
 * Tests entity list functionality.
 *
 * @group helfi_tpr
 */
class ErrandServiceListTest extends MigrationTestBase {

  use ApiTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'views',
    'helfi_tpr',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests collection route (views).
   */
  public function testList() : void {
    // Make sure anonymous user can't see the entity list.
    $this->drupalGet('/admin/content/integrations/tpr-errand-service');
    $this->assertSession()->statusCodeEquals(403);

    // Make sure logged in user without permissions can't see the entity list.
    $account = $this->createUser();
    $this->drupalLogin($account);
    $this->drupalGet('/admin/content/integrations/tpr-errand-service');
    $this->assertSession()->statusCodeEquals(403);

    // Make sure logged in user with access remote entities overview permission
    // can see the entity list.
    $account = $this->createUser([
      'access remote entities overview'
    ]);
    $this->drupalLogin($account);
    $this->drupalGet('/admin/content/integrations/tpr-errand-service');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('No results found.');
  }

}
