<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\Service;
use Drupal\helfi_tpr\Entity\Unit;
use Drupal\Tests\helfi_api_base\Kernel\MigrationTestBase as ApiMigrationTestBase;
use GuzzleHttp\Psr7\Response;

/**
 * Base class to test TPR migrations.
 */
abstract class MigrationTestBase extends ApiMigrationTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'link',
    'address',
    'text',
    'helfi_tpr',
    'media',
    'telephone',
    'menu_link_content',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();

    $entity_types = [
      'menu_link_content',
      'tpr_unit',
      'tpr_service',
      'tpr_errand_service',
      'tpr_service_channel',
    ];

    foreach ($entity_types as $type) {
      $this->installEntitySchema($type);
    }
    $this->installConfig(['helfi_tpr']);
  }

  /**
   * Create the unit migration.
   *
   * @return array
   *   List of entities.
   */
  protected function createUnitMigration(array $units) : array {
    $responses = [
      new Response(200, [], json_encode($units)),
    ];

    $this->container->set('http_client', $this->createMockHttpClient($responses));
    $this->executeMigration('tpr_unit');
    return Unit::loadMultiple();
  }

  /**
   * Create the service migration.
   *
   * @return array
   *   List of entities.
   */
  protected function createServiceMigration() : array {
    $services = [
      [
        'id' => 1,
        'title' => 'Service 1',
        'unit_ids' => ['1'],
      ],
      [
        'id' => 2,
        'title' => 'Service 2',
      ],
      [
        'id' => 3,
        'title' => 'Service 3',
      ],
    ];
    $responses = [
      new Response(200, [], json_encode($services)),
    ];

    foreach ($services as $service) {
      $id = $service['id'];

      foreach (['fi', 'en', 'sv'] as $language) {
        $item = array_merge($service, [
          'title' => sprintf('Service %s %s', $language, $id),
          'description_short' => sprintf('Description short %s %s', $language, $id),
          'description_long' => sprintf('Description long %s %s', $language, $id),
          'exact_errand_services' => [
            123,
            456,
          ],
          'links' => [
            [
              'type' => 'INTERNET',
              'title' => sprintf('0: %s link title %s', $language, $id),
              'url' => sprintf('https://localhost/1/%s/%s', $language, $id),
            ],
            [
              'type' => 'INTERNET',
              'title' => sprintf('1: %s link title %s', $language, $id),
              'url' => sprintf('https://localhost/2/%s/%s', $language, $id),
            ],
          ],
        ]);
        $responses[] = new Response(200, [], json_encode($item));
      }
    }

    $this->container->set('http_client', $this->createMockHttpClient($responses));
    $this->executeMigration('tpr_service');
    return Service::loadMultiple();
  }

}
