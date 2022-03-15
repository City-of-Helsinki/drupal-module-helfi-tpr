<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Tests\helfi_api_base\Functional\MigrationTestBase;
use Drupal\Tests\helfi_api_base\Traits\ApiTestTrait;
use Drupal\Tests\helfi_api_base\Traits\WebServerTestTrait;
use Drupal\Tests\helfi_tpr\Traits\TprMigrateTrait;

/**
 * Tests entity list functionality.
 *
 * @group helfi_tpr
 */
abstract class ListTestBase extends MigrationTestBase {

  use ApiTestTrait;
  use WebServerTestTrait;
  use TprMigrateTrait;

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
   * The entity type.
   *
   * @var string
   */
  protected string $entityType;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    $this->startWebServer();

    // Use our mock server as canonical url.
    $this->config('helfi_tpr.migration_settings.' . $this->entityType)
      ->set('canonical_url', $this->getMockWebServerBaseUrl($this->entityType))
      ->save();
    $this->populateMockQueue();
  }

  /**
   * Populates the mock queue.
   */
  protected function populateMockQueue() : void {
  }

  /**
   * Updates a list of entities.
   *
   * @param string $langcode
   *   The language code of entity to update.
   *
   * @return array
   *   An array of assertion data.
   */
  protected function assertUpdateListEntity(string $langcode) : array {
    return [];
  }

  /**
   * Asserts that we can run 'update' action.
   *
   * @param array $languages
   *   The languages to test.
   */
  protected function assertUpdateAction(array $languages = ['fi', 'en', 'sv']) : void {
    foreach ($languages as $language) {
      // Make sure we can run 'update' action on multiple entities.
      $expected = $this->assertUpdateListEntity($language);

      $query = [
        'language' => $language,
        // Our list view shows items in all languages by default, but can
        // be filtered with a langcode={langcode} query parameter.
        'langcode' => $language,
        'order' => 'id',
        'sort' => 'desc',
      ];

      $this->drupalGet($this->adminListPath, [
        'query' => $query,
      ]);

      // Make sure we can see the placeholder label set in
      // ::assertUpdateListEntity() before we run the update
      // action.
      foreach ($expected as $data) {
        $this->assertSession()->pageTextContains($data['placeholderLabel']);
      }

      $form_data = [
        'action' => "{$this->entityType}_update_action",
        "{$this->entityType}_bulk_form[0]" => 1,
        "{$this->entityType}_bulk_form[1]" => 1,
      ];
      $this->submitForm($form_data, 'Apply to selected items');

      // Make sure data is updated back to what it should be after we run the
      // migration.
      foreach ($expected as $data) {
        $this->assertSession()->pageTextNotContains($data['placeholderLabel']);
        $this->assertSession()->pageTextContains($data['label']);
      }
    }
  }

  /**
   * Asserts that list contains given items.
   *
   * @param array $expected
   *   An array of expected values.
   */
  protected function assertExpectedListItems(array $expected) : void {
    foreach ($expected as $language => $items) {
      [
        'numItems' => $total,
        'expectedTitles' => $expectedTitles,
      ] = $items;

      $this->drupalGet($this->adminListPath, [
        'query' => [
          'order' => 'id',
          // Our list view shows items in all languages by default, but can
          // be filtered with a langcode={langcode} query parameter.
          'langcode' => $language,
          'language' => $language,
          'sort' => 'desc',
        ],
      ]);

      foreach ($expectedTitles as $title) {
        $this->assertSession()->pageTextContains($title);
      }
      $this->assertSession()->pageTextContains(sprintf('Displaying %d - %d of %d', ($total > 0 ? 1 : 0), $total, $total));
    }
  }

  /**
   * Tests list view permissions.
   */
  protected function assertListPermissions() : void {
    // Make sure anonymous user can't see the entity list.
    $this->drupalGet($this->adminListPath);
    $this->assertSession()->statusCodeEquals(403);

    // Make sure logged-in user without permissions can't see the entity list.
    $account = $this->createUser();
    $this->drupalLogin($account);
    $this->drupalGet($this->adminListPath);
    $this->assertSession()->statusCodeEquals(403);

    // Make sure logged-in user with `access remote entities overview`
    // permission can see the entity list.
    $account = $this->createUser($this->listPermissions);
    $this->drupalLogin($account);
    $this->drupalGet($this->adminListPath);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('No results found.');
  }

  /**
   * Make sure publish action works.
   *
   * @param array $languages
   *   The languages to test.
   */
  protected function assertPublishAction(array $languages = ['fi', 'sv', 'en']) : void {
    foreach ($languages as $language) {
      $this->drupalGet($this->adminListPath, [
        'query' => [
          'order' => 'id',
          // Our list view shows items in all languages by default, but can
          // be filtered with a langcode={langcode} query parameter.
          'langcode' => $language,
          'language' => $language,
          'sort' => 'desc',
        ],
      ]);
      // Make sure we can use actions to publish and unpublish content.
      $actions = [
        "{$this->entityType}_publish_action" => TRUE,
        "{$this->entityType}_unpublish_action" => FALSE,
      ];

      foreach ($actions as $action => $published) {
        $form_data = [
          'action' => $action,
          $this->entityType . '_bulk_form[0]' => 1,
          $this->entityType . '_bulk_form[1]' => 1,
        ];
        $this->submitForm($form_data, 'Apply to selected items');

        for ($i = 1; $i <= 2; $i++) {
          $this->assertPublished($i, $published);
        }
      }
    }
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
