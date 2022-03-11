<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\helfi_tpr\Entity\Service;
use Drupal\Tests\helfi_api_base\Traits\ApiTestTrait;
use Drupal\Tests\helfi_tpr\Traits\TprMigrateTrait;
use GuzzleHttp\Psr7\Response;

/**
 * Tests Service entity's list functionality.
 *
 * @group helfi_tpr
 */
class ServiceListTest extends ListTestBase {

  use ApiTestTrait;
  use TprMigrateTrait;

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
   * Update service data.
   *
   * @param int $id
   *   The id.
   * @param string $langcode
   *   The langcode.
   */
  private function updateService(int $id, string $langcode) : void {
    $expected = [
      'name' => sprintf('Service %s %s', $id, $langcode),
      'description' => sprintf('Description %s %s', $id, $langcode),
      'summary' => sprintf('Summary %s %s', $id, $langcode),
    ];
    $service = Service::load($id)->getTranslation($langcode);
    $service->set('name', $expected['name'])
      ->set('description', [
        'value' => $expected['description'],
        'summary' => $expected['summary'],
      ])
      ->set('errand_services', [])
      ->set('links', [])
      ->save();

    $service = Service::load($id)->getTranslation($langcode);
    $this->assertEquals($expected['name'], $service->label());
    $this->assertEquals($expected['description'], $service->get('description')->value);
    $this->assertEquals($expected['summary'], $service->get('description')->summary);
    $this->assertEquals(0, $service->get('errand_services')->count());
    $this->assertEquals(0, $service->get('links')->count());

  }

  /**
   * Tests list view permissions, updating, and publishing services.
   */
  public function testList() : void {
    $this->assertListPermissions();

    // Migrate entities and make sure we can see all entities from fixture.
    $responses = $this->fixture('tpr_service')->getMockResponses();

    // Responses for migrate update action.
    $services = array_filter($this->fixture('tpr_service')->getMockData(), function (array $service) {
      return $service['fi']['id'] === 7822 || $service['fi']['id'] === 7716;
    });

    foreach ($services as $service) {
      $responses[] = new Response(200, [], json_encode($service));
    }

    $this->runServiceMigrate($responses);

    $expected = ['fi' => 6, 'en' => 4, 'sv' => 4];

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
    $this->updateService(7822, 'fi');
    $this->updateService(7716, 'fi');
    $query = [
      'language' => 'fi',
      'langcode' => 'fi',
      'order' => 'id',
      'sort' => 'desc',
    ];
    $this->drupalGet($this->adminListPath, [
      'query' => $query,
    ]);
    $this->assertSession()->linkExists('Service 7822 fi');
    $this->assertSession()->linkExists('Service 7716 fi');

    $form_data = [
      'action' => 'tpr_service_update_action',
      'tpr_service_bulk_form[0]' => 1,
      'tpr_service_bulk_form[1]' => 1,
    ];
    $this->submitForm($form_data, 'Apply to selected items');

    // Make sure data is updated visually when we run the individual
    // migration.
    $this->assertSession()->linkNotExists('Service 7822 fi');
    $this->assertSession()->linkNotExists('Service 7716 fi');
    $this->assertSession()->linkExists('Digituki');
    $this->assertSession()->linkExists('Parkletit');

    $storage = \Drupal::entityTypeManager()->getStorage('tpr_service');
    // Make sure service data is updated back to normal.
    foreach ($services as $service) {
      $service = $service['fi'];
      $storage->resetCache([$service['id']]);
      $entity = $storage->load($service['id'])->getTranslation('fi');

      $this->assertEquals($service['title'], $entity->label());
      $this->assertEquals(count($service['exact_errand_services']), $entity->get('errand_services')->count());
      $this->assertEquals(count($service['links']), $entity->get('links')->count());
    }

    // Make sure we can use actions to publish and unpublish content.
    $this->assertPublishAction('tpr_service', $query);
  }

}
