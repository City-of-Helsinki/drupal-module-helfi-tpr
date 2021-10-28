<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Core\Url;
use Drupal\Tests\helfi_tpr\Traits\TprMigrateTrait;

/**
 * Tests menu link creation from service entity form.
 *
 * @group helfi_tpr
 */
class ServiceMenuLinkTest extends MigrationTestBase {

  use TprMigrateTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'menu_link_content',
    'menu_ui',
  ];

  /**
   * Tests menu link creation.
   */
  public function testMenuLink() : void {
    $role = $this->drupalCreateRole([
      'administer menu',
    ]);
    $this->drupalLogin($this->privilegedAccount);

    $this->runUnitMigrate();
    $this->runServiceMigrate();

    foreach (['fi', 'sv'] as $language) {
      $this->drupalGet(Url::fromRoute('entity.tpr_service.edit_form', ['tpr_service' => 1]), [
        'query' => ['language' => $language],
      ]);
      $this->assertSession()->statusCodeEquals(200);
      // Make sure we can't see menu without permission.
      $this->assertSession()->fieldNotExists('menu[enabled]');
    }

    // Add role to allow our user to edit menus.
    $this->privilegedAccount->addRole($role);
    $this->privilegedAccount->save();

    foreach (['fi', 'sv'] as $language) {
      $this->drupalGet(Url::fromRoute('entity.tpr_service.edit_form', ['tpr_service' => 1]), [
        'query' => ['language' => $language],
      ]);
      // Make sure menu link is not enabled by default.
      $this->assertSession()->checkboxNotChecked('menu[enabled]');

      $this->submitForm([
        'menu[enabled]' => TRUE,
        'menu[title]' => "Menu link $language",
      ], 'Save');

      $this->drupalGet(Url::fromRoute('entity.tpr_service.edit_form', ['tpr_service' => 1]), [
        'query' => ['language' => $language],
      ]);

      $this->assertSession()->checkboxChecked('menu[enabled]');
      $this->assertSession()->fieldValueEquals('menu[title]', "Menu link $language");

      // Make sure link is disabled by default.
      $this->assertMenuLink("Menu link $language", $language, FALSE);
    }
  }

}
