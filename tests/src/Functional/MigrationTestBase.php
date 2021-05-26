<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Tests\helfi_api_base\Functional\MigrationTestBase as ApiMigrationTestBase;
use Drupal\Tests\helfi_api_base\Traits\ApiTestTrait;
use Drupal\user\UserInterface;

/**
 * Base class for multilingual migration tests.
 */
abstract class MigrationTestBase extends ApiMigrationTestBase {

  use ApiTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'media',
    'block',
    'readonly_field_widget',
    'media_library',
    'entity',
    'helfi_tpr',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The account.
   *
   * @var \Drupal\user\UserInterface
   */
  protected UserInterface $privilegedAccount;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->drupalPlaceBlock('local_tasks_block');
    $this->privilegedAccount = $this->createUser([
      'administer tpr_unit',
      'administer tpr_service',
      'translate editable entities',
      'view all tpr_unit revisions',
      'view all tpr_service revisions',
      'revert all tpr_unit revisions',
      'revert all tpr_service revisions',
    ]);
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
  protected function assertMenuLink(string $expected_link, string $language, bool $is_checked) : void {
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
