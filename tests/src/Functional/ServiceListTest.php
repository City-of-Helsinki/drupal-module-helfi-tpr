<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use donatj\MockWebServer\Response;
use Drupal\helfi_tpr\Entity\Service;

/**
 * Tests Service entity's list functionality.
 *
 * @group helfi_tpr
 */
class ServiceListTest extends ListTestBase {

  /**
   * {@inheritdoc}
   */
  protected string $entityType = 'tpr_service';

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
   * {@inheritdoc}
   */
  protected function populateMockQueue(): void {
    foreach ($this->fixture($this->entityType)->getMockData() as $item) {
      $url = sprintf('/%s/%s', $this->entityType, $item['fi']['id']);
      $this->webServer
        ->setResponseOfPath($url, new Response(json_encode($item['fi'])));
    }
  }

  /**
   * Update service data.
   *
   * @param int $id
   *   The id.
   * @param string $langcode
   *   The langcode.
   */
  private function updateListEntity(int $id, string $langcode) : void {
    $expected = [
      'name' => sprintf('Service %s %s', $id, $langcode),
      'description' => sprintf('Description %s %s', $id, $langcode),
      'summary' => sprintf('Summary %s %s', $id, $langcode),
    ];
    $entity = Service::load($id)->getTranslation($langcode);
    $entity->set('name', $expected['name'])
      ->set('description', [
        'value' => $expected['description'],
        'summary' => $expected['summary'],
      ])
      ->set('errand_services', [])
      ->set('links', [])
      ->save();

    $entity = Service::load($id)->getTranslation($langcode);
    $this->assertEquals($expected['name'], $entity->label());
    $this->assertEquals($expected['description'], $entity->get('description')->value);
    $this->assertEquals($expected['summary'], $entity->get('description')->summary);
    $this->assertEquals(0, $entity->get('errand_services')->count());
    $this->assertEquals(0, $entity->get('links')->count());

  }

  /**
   * Tests list view permissions, updating, and publishing services.
   */
  public function testList() : void {
    $this->assertListPermissions();
    $this->runServiceMigrate();

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
    $this->updateListEntity(7822, 'fi');
    $this->updateListEntity(7716, 'fi');
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

    $storage = \Drupal::entityTypeManager()->getStorage($this->entityType);
    $items = $this->fixture($this->entityType)->getMockData();
    // Make sure service data is updated back to normal.
    foreach ($items as $item) {
      $item = $item['fi'];
      $storage->resetCache([$item['id']]);
      $entity = $storage->load($item['id'])->getTranslation('fi');

      $this->assertEquals($item['title'], $entity->label());
      $this->assertEquals(count($item['exact_errand_services']), $entity->get('errand_services')->count());
      $this->assertEquals(count($item['links']), $entity->get('links')->count());
    }

    // Make sure we can use actions to publish and unpublish content.
    $this->assertPublishAction($this->entityType, $query);
  }

}
