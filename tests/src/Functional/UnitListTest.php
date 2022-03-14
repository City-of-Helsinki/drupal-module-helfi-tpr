<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use donatj\MockWebServer\Response;
use Drupal\helfi_tpr\Entity\Unit;

/**
 * Tests Unit entity's list functionality.
 *
 * @group helfi_tpr
 */
class UnitListTest extends ListTestBase {

  /**
   * {@inheritdoc}
   */
  protected string $entityType = 'tpr_unit';

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
   * {@inheritdoc}
   */
  protected function populateMockQueue(): void {
    foreach ($this->fixture($this->entityType)->getMockData() as $item) {
      $url = sprintf('/%s/%s', $this->entityType, $item['id']);
      $this->webServer
        ->setResponseOfPath($url, new Response(json_encode($item)));
    }
  }

  /**
   * Modifies the unit with random data.
   *
   * @param int $unitId
   *   The unit id to update.
   * @param string $langcode
   *   The langcode.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function updateListEntity(int $unitId, string $langcode) : void {
    $expected = [
      'name' => sprintf('Test %s %s', $unitId, $langcode),
      'description' => sprintf('Description %s %s', $unitId, $langcode),
      'summary' => sprintf('Summary %s %s', $unitId, $langcode),
    ];
    $entity = Unit::load($unitId)->getTranslation($langcode);
    $entity->set('name', $expected['name'])
      ->set('services', [])
      ->set('description', [
        'value' => $expected['description'],
        'summary' => $expected['summary'],
      ])
      ->set('accessibility_sentences', [])
      ->save();

    $entity = Unit::load($unitId)->getTranslation($langcode);
    $this->assertEquals($expected['name'], $entity->label());
    $this->assertEquals($expected['description'], $entity->get('description')->value);
    $this->assertEquals($expected['summary'], $entity->get('description')->summary);
    $this->assertEquals(0, $entity->get('accessibility_sentences')->count());
    $this->assertEquals(0, $entity->get('services')->count());
  }

  /**
   * Tests list view permissions, and viewing, updating, and publishing units.
   */
  public function testList() : void {
    $this->assertListPermissions();
    $this->runUnitMigrate();

    $expected = ['fi' => 5, 'en' => 4, 'sv' => 4];

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
    $this->updateListEntity(67763, 'fi');
    $this->updateListEntity(63115, 'fi');
    $query = [
      'language' => 'fi',
      'langcode' => 'fi',
      'order' => 'id',
      'sort' => 'desc',
    ];
    $this->drupalGet($this->adminListPath, [
      'query' => $query,
    ]);
    $this->assertSession()->linkExists('Test 67763 fi');
    $this->assertSession()->linkExists('Test 63115 fi');

    $form_data = [
      'action' => 'tpr_unit_update_action',
      'tpr_unit_bulk_form[0]' => 1,
      'tpr_unit_bulk_form[1]' => 1,
    ];
    $this->submitForm($form_data, 'Apply to selected items');

    // Make sure data is updated visually when we run the individual
    // migration.
    $this->assertSession()->linkNotExists('Test 67763 fi');
    $this->assertSession()->linkNotExists('Test 63115 fi');
    $this->assertSession()->linkExists('Peijaksen sairaala');
    $this->assertSession()->linkExists('Lippulaivan kirjasto');

    $storage = \Drupal::entityTypeManager()->getStorage($this->entityType);
    $items = $this->fixture($this->entityType)->getMockData();

    // Make sure entity data is updated back to normal.
    foreach ($items as $item) {
      $storage->resetCache([$item['id']]);
      $entity = $storage->load($item['id'])->getTranslation('fi');

      $this->assertEquals($item['name_fi'], $entity->label());
      $this->assertNotEquals(sprintf('Description %s fi', $item['id']), $entity->get('description')->value);
      $this->assertNotEquals(sprintf('Summary %s fi', $item['id']), $entity->get('description')->summary);
      $this->assertEquals(count($item['service_descriptions']), $entity->get('services')->count());
      $this->assertEquals(count($item['accessibility_sentences']), $entity->get('accessibility_sentences')->count());
    }

    // Make sure we can use actions to publish and unpublish content.
    $this->assertPublishAction($this->entityType, $query);
  }

}
