<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Core\Url;
use Drupal\Tests\helfi_tpr\Traits\TprMigrateTrait;

/**
 * Tests unit category permissions.
 *
 * @group helfi_tpr
 */
class UnitCategoryPermissionTest extends MigrationTestBase {

  use TprMigrateTrait;

  /**
   * Tests unit category permissions for unit updates.
   */
  public function testCategoryPermissions() : void {
    $this->runUnitMigrate();
    $entityId = 1;

    // Test that privileged account always has access.
    $this->drupalLogin($this->privilegedAccount);
    $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => $entityId]));
    $this->assertSession()->statusCodeEquals(200);

    // Test that anonymous user has no access.
    $this->drupalLogout();
    $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => $entityId]));
    $this->assertSession()->statusCodeEquals(403);

    // Test that logged-in user without special permissions has no access.
    $this->drupalLogin($this->createUser());
    $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => $entityId]));
    $this->assertSession()->statusCodeEquals(403);

    // Test that user with special category-related permission has access.
    $this->drupalLogin($this->createUser([
      'admin daycare units',
    ]));
    $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => $entityId]));
    $this->assertSession()->statusCodeEquals(200);

    // Test that user with wrong category-related permission has no access.
    $this->drupalLogin($this->createUser([
      'admin playground units',
    ]));
    $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => $entityId]));
    $this->assertSession()->statusCodeEquals(403);
  }

}
