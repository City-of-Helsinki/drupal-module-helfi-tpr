<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Fixture;

use Drupal\helfi_api_base\Fixture\FixtureBase;
use GuzzleHttp\Psr7\Response;

/**
 * Provides fixture data for tpr_errand_service migration.
 */
final class ErrandService extends FixtureBase {

  /**
   * Gets the fields used in migration.
   *
   * @return string[]
   *   The fields.
   */
  public function getFields() : array {
    return [
      'name',
      'description',
      'process_description',
      'processing_time',
      'information',
      'expiration_time',
      'costs',
    ];
  }

  /**
   * Gets the mock data.
   *
   * @return array
   *   The mock data.
   */
  public function getMockData() : array {
    $eServices = [
      ['id' => 1],
      ['id' => 2],
      ['id' => 3],
    ];

    foreach ($eServices as $key => $service) {
      $id = $service['id'];

      foreach (['fi', 'sv', 'en'] as $language) {
        $service['links'] = [
          [
            'title' => sprintf('0:%s link title %s', $language, $id),
            'url' => sprintf('https://localhost/0/%s/%s', $language, $id),
          ],
          [
            'title' => sprintf('1:%s link title %s', $language, $id),
            'url' => sprintf('https://localhost/1/%s/%s', $language, $id),
          ],
          [
            'title' => sprintf('2:%s broken link title %s', $language, $id),
            'url' => sprintf(' https://localhost/2/%s/%s ', $language, $id),
          ],
        ];

        $channelId = $id * 3;
        $channels = [];

        for ($i = 1; $i <= 2; $i++) {
          $channelId++;

          $channels[] = [
            'id' => $channelId,
            'name' => sprintf('Channel %s %s %s', $id, $language, $channelId),
            'type_string' => sprintf('%s email %s %s', $id, $language, $channelId),
            'email' => sprintf(' %s.email.%s.%s@hel.fi ', $id, $language, $channelId),
            'phone' => sprintf(' %s-123456-%s-%s ', $id, $language, $channelId),
            'call_charge_info' => sprintf('call_charge_info %s %s %s', $id, $language, $channelId),
            'information' => sprintf('information %s %s %s', $id, $language, $channelId),
            'availabilities' => [
              [
                'availability_string' => sprintf('0:%s:test %s %s', $id, $language, $channelId),
              ],
              [
                'availability_string' => sprintf('1:%s:test %s %s', $id, $language, $channelId),
              ],
            ],
            'links' => [
              [
                'title' => sprintf('0:%s:%s link title %s', $id, $language, $channelId),
                'url' => sprintf('https://localhost/0/%s/%s/%s', $id, $language, $channelId),
              ],
              [
                'title' => sprintf('1:%s:%s link title %s', $id, $language, $channelId),
                'url' => sprintf('https://localhost/1/%s/%s/%s', $id, $language, $channelId),
              ],
              [
                'title' => sprintf('2:%s:%s link title %s', $id, $language, $channelId),
                'url' => sprintf(' https://localhost/2/%s/%s/%s ', $id, $language, $channelId),
              ],
            ],
            'type' => 'EMAIL',
            'requires_authentication' => TRUE,
            'saved_to_customer_folder' => TRUE,
            'e_processing' => TRUE,
            'e_decision' => TRUE,
            'payment_enabled' => TRUE,
            'for_personal_customer' => TRUE,
            'for_corporate_customer' => TRUE,
          ];
        }
        $service['channels'] = $channels;

        foreach ($this->getFields() as $field) {
          $service[$field] = sprintf('%s %s %s', $field, $language, $id);
        }
        $eServices[$key][$language] = $service;
      }
    }
    return $eServices;
  }

  /**
   * {@inheritdoc}
   */
  public function getMockResponses() : array {
    $services = $this->getMockData();
    return [
      new Response(200, [], json_encode($services)),
    ];
  }

}
