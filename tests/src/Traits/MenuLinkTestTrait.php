<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Traits;

/**
 * Provides shared functionality for TPR entity tests.
 */
trait MenuLinkTestTrait {

  /**
   * Places the main menu block.
   */
  protected function placeMainMenuBlock() : void {
    $this->placeBlock('system_menu_block:main', ['id' => 'main-menu']);
  }

  /**
   * Gets the expected main menu link.
   *
   * @param string $expectedValue
   *   The expected menu link.
   *
   * @return \Behat\Mink\Element\NodeElement|null
   *   The link element or null.
   */
  protected function getExpectedMainMenuLink(string $expectedValue) {
    $items = $this
      ->getSession()
      ->getPage()
      ->findAll('css', '#block-main-menu a');

    foreach ($items as $item) {
      if ($item->getText() === $expectedValue) {
        return $item;
      }
    }
    return NULL;
  }

  /**
   * Asserts that given menu link exists in main menu.
   *
   * @param string $expectedValue
   *   The expected value.
   */
  protected function assertMainMenuLinkExists(string $expectedValue) : void {
    $this->assertSession()->statusCodeEquals(200);
    static::assertNotNull($this->getExpectedMainMenuLink($expectedValue));
  }

  /**
   * Asserts that main menu link does not exist.
   *
   * @param string $expectedValue
   *   The expected value.
   */
  protected function assertMainMenuLinkNotExists(string $expectedValue) : void {
    $this->assertSession()->statusCodeEquals(200);
    static::assertNull($this->getExpectedMainMenuLink($expectedValue));
  }

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
  protected function assertMenuLinkEnabled(string $expected_link, string $language, bool $is_checked) : void {
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

}
