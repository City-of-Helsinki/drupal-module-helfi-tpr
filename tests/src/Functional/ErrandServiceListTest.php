<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\TranslatableInterface;
use Drupal\helfi_tpr\Entity\ErrandService;
use donatj\MockWebServer\Response;

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
      'administer remote entities',
      'edit remote entities',
    ];
    $this->adminListPath = '/admin/content/integrations/tpr-errand-service';
  }

  /**
   * {@inheritdoc}
   */
  protected function populateMockQueue(): void {
    /** @var \Drupal\helfi_tpr\Fixture\ErrandService $fixture */
    $fixture = $this->fixture($this->entityType);

    foreach ($fixture->getMockData() as $item) {
      $url = sprintf('/%s/%s', $this->entityType, $item['id']);
      $this->webServer
        ->setResponseOfPath($url, new Response(json_encode($item['fi'])));
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function assertUpdateListEntity(string $langcode) : array {
    $assertionData = [];

    foreach ([3, 2] as $id) {
      $expected = [
        'name' => sprintf('Test %s %s', $id, $langcode),
        'description' => sprintf('Description %s %s', $id, $langcode),
      ];
      $entity = ErrandService::load($id)->getTranslation($langcode);

      $assertionData[$id] = [
        'label' => $entity->label(),
        'placeholderLabel' => $expected['name'],
      ];

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
    return $assertionData;
  }

  /**
   * Tests list view permissions, and viewing, updating, and publishing.
   */
  public function testList() : void {
    $this->assertListPermissions();
    $this->runErrandServiceMigration();

    $this->assertExpectedListItems([
      'fi' => [
        'numItems' => 3,
        'expectedTitles' => [
          'name fi 1',
          'name fi 2',
          'name fi 3',
        ],
      ],
      'en' => [
        'numItems' => 3,
        'expectedTitles' => [
          'name en 1',
          'name en 2',
          'name en 3',
        ],
      ],
      'sv' => [
        'numItems' => 3,
        'expectedTitles' => [
          'name sv 1',
          'name sv 2',
          'name sv 3',
        ],
      ],
    ]);

    // Make sure we can run 'update' action.
    // @todo Test other languages as well.
    $this->assertUpdateAction(['fi']);

    $storage = \Drupal::entityTypeManager()->getStorage('tpr_errand_service');
    /** @var \Drupal\helfi_tpr\Fixture\ErrandService $fixture */
    $fixture = $this->fixture($this->entityType);
    $items = $fixture->getMockData();

    // Make sure entity data is updated back to normal.
    foreach ($items as $item) {
      $item = $item['fi'];
      $storage->resetCache([$item['id']]);
      $entity = $storage->load($item['id']);
      assert($entity instanceof TranslatableInterface);
      assert($entity instanceof ContentEntityInterface);

      $entity = $entity->getTranslation('fi');

      $this->assertEquals($item['name'], $entity->label());
      $this->assertNotEquals(sprintf('Description %s fi', $item['id']), $entity->get('description')->value);
      $this->assertEquals(count($item['channels']), $entity->get('channels')->count());
      $this->assertEquals(count($item['links']), $entity->get('links')->count());
    }

    // Make sure we can use actions to publish and unpublish content.
    $this->assertPublishAction();
  }

}
