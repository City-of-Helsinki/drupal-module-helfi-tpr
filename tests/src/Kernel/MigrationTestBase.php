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
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();

    $entity_types = [
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
  protected function createUnitMigration() : array {
    $units = [
      [
        'id' => 1,
        'name_fi' => 'Name fi 1',
        'name_sv' => 'Name sv 1',
        'name_en' => 'Name en 1',
        'latitude' => '60.19',
        'longitude' => '24.76',
        'street_address_fi' => 'Address fi 1',
        'street_address_sv' => 'Address sv 1',
        'address_zip' => '02180',
        'address_city_fi' => 'Espoo fi 1',
        'address_city_sv' => 'Espoo sv 1',
        'address_city_en' => 'Espoo en 1',
        'phone' => '+3581234',
        'call_charge_info_fi' => 'pvm fi 1',
        'call_charge_info_en' => 'pvm en 1',
        'call_charge_info_sv' => 'pvm sv 1',
        'www_fi' => 'https://localhost/fi/1',
        'www_sv' => 'https://localhost/sv/1',
        'www_en' => 'https://localhost/en/1',
        'created_time' => '2015-11-03T12:03:45',
        'modified_time' => '2015-11-03T12:03:45',
      ],
    ];
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
