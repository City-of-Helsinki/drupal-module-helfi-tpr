<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\helfi_tpr\Entity\Unit;
use Drupal\Tests\helfi_api_base\Traits\ApiTestTrait;
use Drupal\Tests\helfi_tpr\Traits\TprMigrateTrait;
use GuzzleHttp\Psr7\Response;

/**
 * Tests Unit entity's list functionality.
 *
 * @group helfi_tpr
 */
class UnitListTest extends ListTestBase {

  use ApiTestTrait;
  use TprMigrateTrait;

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
   * Modifies the unit with random data.
   *
   * @param int $unitId
   *   The unit id to update.
   * @param string $langcode
   *   The langcode.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function updateUnit(int $unitId, string $langcode) : void {
    $expected = [
      'name' => sprintf('Test %s %s', $unitId, $langcode),
      'description' => sprintf('Description %s %s', $unitId, $langcode),
      'summary' => sprintf('Summary %s %s', $unitId, $langcode),
    ];
    $unit = Unit::load($unitId)->getTranslation($langcode);
    $unit->set('name', $expected['name'])
      ->set('services', [])
      ->set('description', [
        'value' => $expected['description'],
        'summary' => $expected['summary'],
      ])
      ->set('accessibility_sentences', [])
      ->save();

    $unit = Unit::load($unitId)->getTranslation($langcode);
    $this->assertEquals($expected['name'], $unit->label());
    $this->assertEquals($expected['description'], $unit->get('description')->value);
    $this->assertEquals($expected['summary'], $unit->get('description')->summary);
    $this->assertEquals(0, $unit->get('accessibility_sentences')->count());
    $this->assertEquals(0, $unit->get('services')->count());
  }

  /**
   * Tests list view permissions, and viewing, updating, and publishing units.
   */
  public function testList() : void {
    $this->assertListPermissions();

    // Response for initial migration.
    $responses = $this->fixture('tpr_unit')->getMockResponses();

    $units = array_filter($this->fixture('tpr_unit')->getMockData(), function (array $unit) {
      return $unit['id'] === 67763 || $unit['id'] === 63115;
    });

    // Responses for migrate update action.
    foreach ($units as $unit) {
      $responses[] = new Response(200, [], json_encode($unit));
      // Connections and accessibility sentences requests.
      $responses[] = new Response(200, [], json_encode([]));
      $responses[] = new Response(200, [], json_encode([]));
    }
    // Migrate entities and make sure we can see all entities from fixture.
    $this->runUnitMigrate($responses);

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
    $this->updateUnit(67763, 'fi');
    $this->updateUnit(63115, 'fi');
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

    $storage = \Drupal::entityTypeManager()->getStorage('tpr_unit');
    // Make sure unit data is updated back to normal.
    foreach ($units as $unit) {
      $storage->resetCache([$unit['id']]);
      $entity = $storage->load($unit['id'])->getTranslation('fi');

      $this->assertEquals($unit['name_fi'], $entity->label());
      $this->assertNotEquals(sprintf('Description %s fi', $unit['id']), $entity->get('description')->value);
      $this->assertNotEquals(sprintf('Summary %s fi', $unit['id']), $entity->get('description')->summary);
      $this->assertEquals(count($unit['service_descriptions']), $entity->get('services')->count());
      $this->assertEquals(count($unit['accessibility_sentences']), $entity->get('accessibility_sentences')->count());
    }

    // Make sure we can use actions to publish and unpublish content.
    $this->assertPublishAction('tpr_unit', $query);
  }

}
