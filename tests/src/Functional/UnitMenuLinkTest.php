<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Core\Url;
use Drupal\Tests\helfi_api_base\Traits\ApiTestTrait;
use Drupal\Tests\helfi_tpr\Traits\MenuLinkTestTrait;
use Drupal\Tests\helfi_tpr\Traits\TprMigrateTrait;

/**
 * Tests menu link creation from unit entity form.
 *
 * @group helfi_tpr
 */
class UnitMenuLinkTest extends MigrationTestBase {

  use TprMigrateTrait;
  use ApiTestTrait;
  use MenuLinkTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'menu_link_content',
    'menu_ui',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {
    parent::setUp();

    $this->placeMainMenuBlock();
    $this->enableTranslation(['menu_link_content']);
    $this->rebuildContainer();
  }

  /**
   * Tests menu link creation.
   */
  public function testMenuLink() : void {
    $role = $this->drupalCreateRole([
      'administer menu',
    ]);
    $this->drupalLogin($this->privilegedAccount);

    $this->runUnitMigrate();

    foreach (['fi', 'sv'] as $language) {
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 1]), [
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
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 1]), [
        'query' => ['language' => $language],
      ]);
      // Make sure menu link is not enabled by default.
      $this->assertSession()->checkboxNotChecked('menu[enabled]');

      $this->submitForm([
        'menu[enabled]' => TRUE,
        'menu[title]' => "Menu link $language",
      ], 'Save');

      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 1]), [
        'query' => ['language' => $language],
      ]);

      $this->assertSession()->checkboxChecked('menu[enabled]');
      $this->assertSession()->fieldValueEquals('menu[title]', "Menu link $language");

      // Make sure link is enabled by default.
      $this->assertMenuLinkEnabled("Menu link $language", $language, TRUE);

      // Make sure menu link is visible in main menu.
      $this->drupalGet(Url::fromRoute('<front>'), [
        'query' => ['language' => $language],
      ]);
      $this->assertMainMenuLinkExists("Menu link $language");
    }

    // Make sure menu link to unpublished entity is not visible to logged-out
    // user.
    $this->drupalLogout();
    foreach (['fi', 'sv'] as $language) {
      $this->drupalGet(Url::fromRoute('<front>'), [
        'query' => ['language' => $language],
      ]);
      $this->assertMainMenuLinkNotExists("Menu link $language");
    }
  }

}
