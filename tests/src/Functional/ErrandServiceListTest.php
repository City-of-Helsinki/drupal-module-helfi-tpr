<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use donatj\MockWebServer\Response;
use Drupal\helfi_tpr\Entity\ErrandService;

/**
 * Tests Errand Service entity's list functionality.
 *
 * @group helfi_tpr
 */
class ErrandServiceListTest extends ListTestBase {

  /**
   * {@inheritdoc}
   */
  protected string $entityType = 'tpr_errand_service';

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();
    $this->listPermissions = [
      'access remote entities overview',
      'edit remote entities',
    ];
    $this->adminListPath = '/admin/content/integrations/tpr-errand-service';
  }

  /**
   * {@inheritdoc}
   */
  protected function populateMockQueue(): void {
    foreach ($this->fixture($this->entityType)->getMockData() as $item) {
      $url = sprintf('/%s/%s', $this->entityType, $item['id']);
      $this->webServer
        ->setResponseOfPath($url, new Response(json_encode($item['fi'])));
    }
  }

  /**
   * Updates the list entity.
   *
   * @param int $id
   *   The entity id.
   * @param string $langcode
   *   The langcode.
   */
  private function updateListEntity(int $id, string $langcode) : void {
    $expected = [
      'name' => sprintf('Test %s %s', $id, $langcode),
      'description' => sprintf('Description %s %s', $id, $langcode),
    ];
    $entity = ErrandService::load($id)->getTranslation($langcode);
    $entity->set('name', $expected['name'])
      ->set('channels', [])
      ->set('description', [
        'value' => $expected['description'],
      ])
      ->set('links', [])
      ->save();

    $entity = ErrandService::load($id)->getTranslation($langcode);

    $this->assertEquals($expected['name'], $entity->label());
    $this->assertEquals($expected['description'], $entity->get('description')->value);
    $this->assertEquals(0, $entity->get('channels')->count());
    $this->assertEquals(0, $entity->get('links')->count());
  }

  /**
   * Tests list view permissions, and viewing, updating, and publishing.
   */
  public function testList() : void {
    $this->assertListPermissions();
    $this->runErrandServiceMigration();

    $expected = ['fi' => 3, 'en' => 3, 'sv' => 3];

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
    $this->updateListEntity(3, 'fi');
    $this->updateListEntity(2, 'fi');

    $query = [
      'language' => 'fi',
      'langcode' => 'fi',
      'order' => 'id',
      'sort' => 'desc',
    ];

    $this->drupalGet($this->adminListPath, [
      'query' => $query,
    ]);
    $this->assertSession()->pageTextContains('Test 3 fi');
    $this->assertSession()->pageTextContains('Test 2 fi');

    $form_data = [
      'action' => 'tpr_errand_service_update_action',
      'tpr_errand_service_bulk_form[0]' => 1,
      'tpr_errand_service_bulk_form[1]' => 1,
    ];
    $this->submitForm($form_data, 'Apply to selected items');

    // Make sure data is updated visually when we run the individual
    // migration.
    $this->assertSession()->pageTextNotContains('Test 3 fi');
    $this->assertSession()->pageTextNotContains('Test 2 fi');
    $this->assertSession()->pageTextContains('name fi 3');
    $this->assertSession()->pageTextContains('name fi 2');

    $storage = \Drupal::entityTypeManager()->getStorage('tpr_errand_service');
    $items = $this->fixture('tpr_errand_service')->getMockData();

    // Make sure entity data is updated back to normal.
    foreach ($items as $item) {
      $item = $item['fi'];
      $storage->resetCache([$item['id']]);
      $entity = $storage->load($item['id'])->getTranslation('fi');

      $this->assertEquals($item['name'], $entity->label());
      $this->assertNotEquals(sprintf('Description %s fi', $item['id']), $entity->get('description')->value);
      $this->assertEquals(count($item['channels']), $entity->get('channels')->count());
      $this->assertEquals(count($item['links']), $entity->get('links')->count());
    }

    // Make sure we can use actions to publish and unpublish content.
    $this->assertPublishAction('tpr_errand_service', $query);
  }

}
