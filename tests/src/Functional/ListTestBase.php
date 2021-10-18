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
abstract class ListTestBase extends MigrationTestBase {

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
   * The path for the migrated content's list page.
   *
   * @var string
   */
  protected string $adminListPath;

  /**
   * The array of the permission names allowing to view the list page.
   *
   * @var array
   */
  protected array $listPermissions;

  /**
   * Tests list view permissions.
   */
  public function testList() : void {
    // Make sure anonymous user can't see the entity list.
    $this->drupalGet($this->adminListPath);
    $this->assertSession()->statusCodeEquals(403);

    // Make sure logged in user without permissions can't see the entity list.
    $account = $this->createUser();
    $this->drupalLogin($account);
    $this->drupalGet($this->adminListPath);
    $this->assertSession()->statusCodeEquals(403);

    // Make sure logged in user with `access remote entities overview`
    // permission can see the entity list.
    $account = $this->createUser($this->listPermissions);
    $this->drupalLogin($account);
    $this->drupalGet($this->adminListPath);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('No results found.');
  }

  /**
   * Asserts that the item is published or unpublished.
   *
   * @param int $nthChild
   *   The nth child.
   * @param bool $published
   *   TRUE if expected to be published.
   */
  protected function assertPublished(int $nthChild, bool $published) : void {
    $element = $this->getSession()
      ->getPage()
      ->find('css', "table tbody tr:nth-of-type($nthChild) .views-field-content-translation-status");

    $this->assertEquals($published ? 'Published' : 'Unpublished', $element->getText());
  }

}
