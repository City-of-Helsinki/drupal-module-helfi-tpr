<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Fixture;

use Drupal\helfi_api_base\Fixture\FixtureBase;
use GuzzleHttp\Psr7\Response;

/**
 * Provides fixture data for tpr_errand_service migration.
 */
final class ErrandService extends FixtureBase {

  /**
   * {@inheritdoc}
   */
  public function getMockResponses() : array {
    $eservices = [
      ['id' => 1],
    ];
    $responses = [
      new Response(200, [], json_encode($eservices)),
    ];

    $fields = [
      'name',
      'description',
      'process_description',
      'processing_time',
      'information',
      'expiration_time',
      'costs',
    ];

    foreach ($eservices as $service) {
      $id = $service['id'];

      foreach (['fi', 'en', 'sv'] as $language) {
        $service['links'] = [
          [
            'title' => sprintf('0:%s link title %s', $language, $id),
            'url' => sprintf('https://localhost/0/%s/%s', $language, $id),
          ],
          [
            'title' => sprintf('1:%s link title %s', $language, $id),
            'url' => sprintf('https://localhost/1/%s/%s', $language, $id),
          ],
        ];

        $service['channels'] = [
          [
            'id' => $id,
            'type_string' => sprintf('email %s %s', $language, $id),
            'email' => sprintf('email.%s.%s@hel.fi', $language, $id),
            'phone' => sprintf('123456-%s-%s', $language, $id),
            'call_charge_info' => sprintf('call_charge_info %s %s', $language, $id),
            'availabilities' => [
              [
                'availability_string' => sprintf('0:test %s %s', $language, $id),
              ],
              [
                'availability_string' => sprintf('1:test %s %s', $language, $id),
              ],
            ],
            'links' => [
              [
                'title' => sprintf('0:%s link title %s', $language, $id),
                'url' => sprintf('https://localhost/0/%s/%s', $language, $id),
              ],
              [
                'title' => sprintf('1:%s link title %s', $language, $id),
                'url' => sprintf('https://localhost/1/%s/%s', $language, $id),
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
          ],
        ];

        foreach ($fields as $field) {
          $service[$field] = sprintf('%s %s %s', $field, $language, $id);
        }
        $responses[] = new Response(200, [], json_encode($service));
      }
    }

    return $responses;
  }

}
