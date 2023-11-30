<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use donatj\MockWebServer\Response;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\TranslatableInterface;
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
    /** @var \Drupal\helfi_tpr\Fixture\Service $fixture */
    $fixture = $this->fixture($this->entityType);

    foreach ($fixture->getMockData() as $item) {
      $url = sprintf('/%s/%s', $this->entityType, $item['fi']['id']);
      $this->webServer
        ->setResponseOfPath($url, new Response(json_encode($item['fi'])));
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function assertUpdateListEntity(string $langcode) : array {
    $assertionData = [];

    foreach ([7822, 7716] as $id) {
      $expected = [
        'name' => sprintf('Service %s %s', $id, $langcode),
        'description' => sprintf('Description %s %s', $id, $langcode),
        'summary' => sprintf('Summary %s %s', $id, $langcode),
      ];
      $entity = Service::load($id)->getTranslation($langcode);

      $assertionData[$id] = [
        'label' => $entity->label(),
        'placeholderLabel' => $expected['name'],
      ];

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
    return $assertionData;
  }

  /**
   * Tests list view permissions, updating, and publishing services.
   */
  public function testList() : void {
    $this->assertListPermissions();
    $this->runServiceMigrate();

    $this->assertExpectedListItems([
      'fi' => [
        'numItems' => 6,
        'expectedTitles' => [
          'Service fi 1',
          'Service fi 2',
          'Service fi 3',
          'Sosiaalineuvonta',
          'Parkletit',
          'Digituki',
        ],
      ],
      'en' => [
        'numItems' => 4,
        'expectedTitles' => [
          'Service en 1',
          'Service en 2',
          'Service en 3',
          'Social welfare counselling',
        ],
      ],
      'sv' => [
        'numItems' => 4,
        'expectedTitles' => [
          'Service sv 1',
          'Service sv 2',
          'Service sv 3',
          'Socialrådgivning',
        ],
      ],
    ]);

    // Make sure we can run 'update' action.
    // @todo Test other languages as well.
    $this->assertUpdateAction(['fi']);

    $storage = \Drupal::entityTypeManager()->getStorage($this->entityType);
    /** @var \Drupal\helfi_tpr\Fixture\Service $fixture */
    $fixture = $this->fixture($this->entityType);
    $items = $fixture->getMockData();

    // Make sure service data is updated back to normal.
    foreach ($items as $item) {
      $item = $item['fi'];
      $storage->resetCache([$item['id']]);
      $entity = $storage->load($item['id']);
      assert($entity instanceof TranslatableInterface);
      assert($entity instanceof ContentEntityInterface);

      $entity = $entity->getTranslation('fi');

      $this->assertEquals($item['title'], $entity->label());
      $this->assertEquals(count($item['exact_errand_services']), $entity->get('errand_services')->count());
      $this->assertEquals(count($item['links']), $entity->get('links')->count());
    }

    // Make sure we can use actions to publish and unpublish content.
    $this->assertPublishAction();
  }

}
