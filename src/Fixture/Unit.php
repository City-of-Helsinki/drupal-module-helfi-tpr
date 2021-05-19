<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Fixture;

use Drupal\helfi_api_base\Fixture\FixtureBase;
use GuzzleHttp\Psr7\Response;

/**
 * Provides fixture data for tpr_unit migration.
 */
final class Unit extends FixtureBase {

  /**
   * Gets the mock data.
   *
   * @return array[]
   *   The mock data.
   */
  public function getMockData() : array {
    return [
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
        'accessibility_sentences' => [
          [
            'sentence_group_name' => 'Group 1',
            'sentence_group_fi' => 'Group fi 1',
            'sentence_group_sv' => 'Group sv 1',
            'sentence_group_en' => 'Group en 1',
            'sentence_fi' => 'Sentence fi 1',
            'sentence_sv' => 'Sentence sv 1',
            'sentence_en' => 'Sentence en 1',
          ],
          [
            'sentence_group_name' => 'Group 2',
            'sentence_group_fi' => 'Group fi 2',
            'sentence_group_sv' => 'Group sv 2',
            'sentence_group_en' => 'Group en 2',
            'sentence_fi' => 'Sentence fi 2',
            'sentence_sv' => 'Sentence sv 2',
            'sentence_en' => 'Sentence en 2',
          ],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getMockResponses() : array {
    $units = $this->getMockData();
    $responses = [
      new Response(200, [], json_encode($units)),
    ];
    foreach ($units as $unit) {
      $responses[] = new Response(200, [], json_encode($unit));
    }
    return $responses;
  }

}
