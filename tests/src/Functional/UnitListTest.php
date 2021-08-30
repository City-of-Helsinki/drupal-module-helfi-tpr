<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\helfi_tpr\Entity\Unit;
use Drupal\Tests\helfi_api_base\Traits\ApiTestTrait;
use GuzzleHttp\Psr7\Response;

/**
 * Tests Unit entity's list functionality.
 *
 * @group helfi_tpr
 */
class UnitListTest extends ListTestBase {

  use ApiTestTrait;

  /**
   * Migrates the tpr unit entities.
   */
  private function runMigrate() : void {
    $units = $this->getFixture('helfi_tpr', 'unit.json');
    $responses = [
      new Response(200, [], $units),
    ];

    foreach (json_decode($units, TRUE) as $unit) {
      $responses[] = new Response(200, [], json_encode($unit));
      // Connections and accessibility sentences requests.
      $responses[] = new Response(200, [], json_encode([]));
      $responses[] = new Response(200, [], json_encode([]));
    }

    $this->container->set('http_client', $this->createMockHttpClient($responses));
    $this->executeMigration('tpr_unit');
  }

  /**
   * Asserts that the item is published or unpublished.
   *
   * @param int $nthChild
   *   The nth child.
   * @param bool $published
   *   TRUE if expected to be published.
   */
  private function assertPublished(int $nthChild, bool $published) : void {
    $element = $this->getSession()
      ->getPage()
      ->find('css', "table tbody tr:nth-of-type($nthChild) .views-field-content-translation-status");

    $this->assertEquals($published ? 'Published' : 'Unpublished', $element->getText());
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();
    $this->listPermissions = [
      'access remote entities overview',
      'administer tpr_unit',
    ];
    $this->adminListPath = '/admin/content/integrations/tpr-unit';
  }

  /**
   * Tests list view permissions, and viewing, updating, and publishing units.
   */
  public function testList() : void {
    parent::testList();

    // Migrate entities and make sure we can see all entities from fixture.
    $this->runMigrate();
    $expected = ['fi' => 6, 'en' => 0, 'sv' => 4];

    foreach ($expected as $language => $total) {
      $this->drupalGet('/admin/content/integrations/tpr-unit', [
        'query' => [
          'langcode' => $language,
          'language' => $language,
        ],
      ]);
      $this->assertSession()->pageTextContains(sprintf('Displaying %d - %d of %d', ($total > 0 ? 1 : 0), $total, $total));
    }

    // Make sure we can run 'update' action on multiple entities.
    Unit::load('22736')->set('name', 'Test 1')->save();
    Unit::load('57331')->set('name', 'Test 2')->save();
    $this->drupalGet('/admin/content/integrations/tpr-unit', [
      'query' => [
        'language' => 'fi',
        'langcode' => 'fi',
        'order' => 'changed',
        'sort' => 'desc',
      ],
    ]);
    $this->assertSession()->pageTextContains('Test 1');
    $this->assertSession()->pageTextContains('Test 2');

    $form_data = [
      'action' => 'tpr_unit_update_action',
      // The list is sorted by changed timestamp so our updated entities
      // should be at the top of the list.
      'tpr_unit_bulk_form[0]' => 1,
      'tpr_unit_bulk_form[1]' => 1,
    ];
    $this->submitForm($form_data, 'Apply to selected items');

    $this->assertSession()->pageTextNotContains('Test 1');
    $this->assertSession()->pageTextNotContains('Test 2');
    $this->assertSession()->pageTextContains('Esteetön testireitti / Leppävaara');
    $this->assertSession()->pageTextContains('InnoOmnia');

    // Make sure we can use actions to publish and unpublish content.
    $actions = [
      'tpr_unit_publish_action' => TRUE,
      'tpr_unit_unpublish_action' => FALSE,
    ];

    foreach ($actions as $action => $published) {
      $form_data = [
        'action' => $action,
        'tpr_unit_bulk_form[0]' => 1,
        'tpr_unit_bulk_form[1]' => 1,
      ];
      $this->submitForm($form_data, 'Apply to selected items');

      for ($i = 1; $i <= 2; $i++) {
        $this->assertPublished($i, $published);
      }
    }
  }

}
