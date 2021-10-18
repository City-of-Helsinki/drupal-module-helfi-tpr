<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\helfi_tpr\Entity\Service;
use Drupal\Tests\helfi_api_base\Traits\ApiTestTrait;
use GuzzleHttp\Psr7\Response;

/**
 * Tests Service entity's list functionality.
 *
 * @group helfi_tpr
 */
class ServiceListTest extends ListTestBase {

  use ApiTestTrait;

  /**
   * Migrates the tpr service entities.
   */
  private function runMigrate() : void {
    $fixture = $this->getFixture('helfi_tpr', 'service.json');
    $responses = [
      new Response(200, [], $fixture),
    ];

    foreach (json_decode($fixture, TRUE) as $item) {
      // Update actions.
      $responses[] = new Response(200, [], json_encode($item));
    }

    $this->container->set('http_client', $this->createMockHttpClient($responses));
    $this->executeMigration('tpr_service');
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();
    $this->listPermissions = [
      'access remote entities overview',
      'administer tpr_service',
    ];
    $this->adminListPath = '/admin/content/integrations/tpr-service';
  }

  /**
   * Tests list view permissions, updating, and publishing services.
   */
  public function testList() : void {
    parent::testList();

    // Migrate entities and make sure we can see all entities from fixture.
    $this->runMigrate();
    $expected = ['fi' => 2, 'en' => 2, 'sv' => 1];

    foreach ($expected as $language => $total) {
      $this->drupalGet($this->adminListPath, [
        'query' => [
          'langcode' => $language,
          'language' => $language,
        ],
      ]);
      $this->assertSession()->pageTextContains(sprintf('Displaying %d - %d of %d', ($total > 0 ? 1 : 0), $total, $total));
    }

    // Make sure we can run 'update' action on multiple entities.
    Service::load('2773')->set('name', 'Test 1')->save();
    $this->drupalGet($this->adminListPath, [
      'query' => [
        'language' => 'fi',
        'langcode' => 'fi',
        'order' => 'changed',
        'sort' => 'desc',
      ],
    ]);
    $this->assertSession()->pageTextContains('Test 1');
    $this->assertSession()->pageTextContains('Koulun kerhotoiminta');

    $form_data = [
      'action' => 'tpr_service_update_action',
      // The list is sorted by changed timestamp so our updated entities
      // should be at the top of the list.
      'tpr_service_bulk_form[0]' => 1,
    ];
    $this->submitForm($form_data, 'Apply to selected items');

    $this->assertSession()->pageTextNotContains('Test 1');
    $this->assertSession()->pageTextContains('Perusopetus');
    $this->assertSession()->pageTextContains('Koulun kerhotoiminta');

    // Make sure we can use actions to publish and unpublish content.
    $actions = [
      'tpr_service_publish_action' => TRUE,
      'tpr_service_unpublish_action' => FALSE,
    ];

    foreach ($actions as $action => $published) {
      $form_data = [
        'action' => $action,
        'tpr_service_bulk_form[0]' => 1,
        'tpr_service_bulk_form[1]' => 1,
      ];
      $this->submitForm($form_data, 'Apply to selected items');

      for ($i = 1; $i <= 2; $i++) {
        $this->assertPublished($i, $published);
      }
    }
  }

}
