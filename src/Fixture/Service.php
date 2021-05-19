<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Fixture;

use Drupal\helfi_api_base\Fixture\FixtureBase;
use GuzzleHttp\Psr7\Response;

/**
 * Provides fixture data for tpr_service migration.
 */
final class Service extends FixtureBase {

  /**
   * {@inheritdoc}
   */
  public function getMockResponses() : array {
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
    return $responses;
  }

}
