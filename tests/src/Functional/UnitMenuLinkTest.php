<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Core\Url;
use Drupal\Tests\helfi_tpr\Traits\UnitMigrateTrait;

/**
 * Tests unit revisions.
 *
 * @group helfi_tpr
 */
class UnitMenuLinkTest extends MigrationTestBase {

  use UnitMigrateTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'menu_link_content',
    'menu_ui',
  ];

  /**
   * Asserts that given menu link exists and is enabled or disabled.
   *
   * @param string $expected_link
   *   The expected link label.
   * @param string $language
   *   The language.
   * @param bool $is_checked
   *   Whether the checkbox is expected to be checked or not.
   */
  private function assertMenuLink(string $expected_link, string $language, bool $is_checked) : void {
    // Make sure link is disabled by default.
    $this->drupalGet('/admin/structure/menu/manage/main', [
      'query' => ['language' => $language],
    ]);
    $this->assertSession()->linkExists($expected_link);
    $element = $this->getSession()->getPage()->find('css', '.checkbox.menu-enabled input[type="checkbox"]');
    $this->assertNotNull($element);

    if ($is_checked) {
      $this->assertTrue($element->isChecked());
    }
    else {
      $this->assertTrue(!$element->isChecked());
    }
  }

  /**
   * Tests menu link creation.
   */
  public function testMenuLink() : void {
    $role = $this->drupalCreateRole([
      'administer menu',
    ]);
    $this->drupalLogin($this->privilegedAccount);

    $units = [
      [
        'id' => 999,
        'name_fi' => 'Name fi',
        'name_sv' => 'Name sv',
        'call_charge_info_fi' => 'Charge fi',
        'call_charge_info_sv' => 'Charge sv',
        'modified_time' => '2015-05-16T20:01:01',
      ],
    ];
    $this->runMigrate($units);

    foreach (['fi', 'sv'] as $language) {
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 999]), [
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
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 999]), [
        'query' => ['language' => $language],
      ]);
      // Make sure menu link is not enabled by default.
      $this->assertSession()->checkboxNotChecked('menu[enabled]');

      $this->submitForm([
        'menu[enabled]' => TRUE,
        'menu[title]' => "Menu link $language",
      ], 'Save');

      $this->assertSession()->checkboxChecked('menu[enabled]');
      $this->assertSession()->fieldValueEquals('menu[title]', "Menu link $language");

      // Make sure link is disabled by default.
      $this->assertMenuLink("Menu link $language", $language, FALSE);

      // Make sure publishing entity also publishes the menu link.
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 999]), [
        'query' => ['language' => $language],
      ]);
      $this->submitForm([
        'content_translation[status]' => TRUE,
      ], 'Save');
      $this->assertMenuLink("Menu link $language", $language, TRUE);
    }
  }

}
