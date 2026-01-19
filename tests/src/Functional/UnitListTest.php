<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\TranslatableInterface;
use Drupal\helfi_tpr\Entity\Unit;
use donatj\MockWebServer\Response;

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
  protected function setUp() : void {
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
    /** @var \Drupal\helfi_tpr\Fixture\Unit $fixture */
    $fixture = $this->fixture($this->entityType);

    foreach ($fixture->getMockData() as $item) {
      $url = sprintf('/%s/%s', $this->entityType, $item['id']);
      $this->webServer
        ->setResponseOfPath($url, new Response(json_encode($item)));
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function assertUpdateListEntity(string $langcode) : array {
    $assertionData = [];

    foreach ([67763, 63115] as $id) {
      $expected = [
        'name' => sprintf('Test %s %s', $id, $langcode),
        'description' => sprintf('Description %s %s', $id, $langcode),
        'summary' => sprintf('Summary %s %s', $id, $langcode),
      ];
      $entity = Unit::load($id)->getTranslation($langcode);

      $assertionData[$id] = [
        'label' => $entity->label(),
        'placeholderLabel' => $expected['name'],
      ];

      $entity->set('name', $expected['name'])
        ->set('services', [])
        ->set('description', [
          'value' => $expected['description'],
          'summary' => $expected['summary'],
        ])
        ->set('accessibility_sentences', [])
        ->save();

      $entity = Unit::load($id)->getTranslation($langcode);
      $this->assertEquals($expected['name'], $entity->label());
      $this->assertEquals($expected['description'], $entity->get('description')->value);
      $this->assertEquals($expected['summary'], $entity->get('description')->summary);
      $this->assertEquals(0, $entity->get('accessibility_sentences')->count());
      $this->assertEquals(0, $entity->get('services')->count());
    }

    return $assertionData;
  }

  /**
   * Tests list view permissions, and viewing, updating, and publishing units.
   */
  public function testList() : void {
    $this->assertListPermissions();
    $this->runUnitMigrate();

    $this->assertExpectedListItems([
      'fi' => [
        'numItems' => 5,
        'expectedTitles' => [
          'Name fi 1',
          'Viikin kampuskirjasto',
          'Otaniemen kirjasto',
          'Lippulaivan kirjasto',
          'Peijaksen sairaala',
        ],
      ],
      'en' => [
        'numItems' => 4,
        'expectedTitles' => [
          'Name en 1',
          'Viikin kampuskirjasto',
          'Otaniemi library',
          'Lippulaiva library',
        ],
      ],
      'sv' => [
        'numItems' => 4,
        'expectedTitles' => [
          'Name sv 1',
          'Viikin kampuskirjasto',
          'Otnäs bibliotek',
          'Lippulaivabiblioteket',
        ],
      ],
    ]);

    // Make sure we can run 'update' action.
    // @todo Test other languages as well.
    $this->assertUpdateAction(['fi']);

    $storage = \Drupal::entityTypeManager()->getStorage($this->entityType);
    /** @var \Drupal\helfi_tpr\Fixture\Unit $fixture */
    $fixture = $this->fixture($this->entityType);
    $items = $fixture->getMockData();

    // Make sure entity data is updated back to normal.
    foreach ($items as $item) {
      $storage->resetCache([$item['id']]);
      $entity = $storage->load($item['id']);
      assert($entity instanceof TranslatableInterface);
      assert($entity instanceof ContentEntityInterface);

      $entity = $entity->getTranslation('fi');

      $this->assertEquals($item['name_fi'], $entity->label());
      $this->assertNotEquals(sprintf('Description %s fi', $item['id']), $entity->get('description')->value);
      $this->assertNotEquals(sprintf('Summary %s fi', $item['id']), $entity->get('description')->summary);
      $this->assertEquals(count($item['service_descriptions']), $entity->get('services')->count());
      $this->assertEquals(count($item['accessibility_sentences']), $entity->get('accessibility_sentences')->count());
    }

    // Make sure we can use actions to publish and unpublish content.
    $this->assertPublishAction();
  }

}
