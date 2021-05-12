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
   * {@inheritdoc}
   */
  public function getMockResponses() : array {
    $units = [
      [
        'id' => 999,
        'name_fi' => 'Name fi',
        'name_sv' => 'Name sv',
        'call_charge_info_fi' => 'Charge fi',
        'call_charge_info_sv' => 'Charge sv',
        'modified_time' => '2015-05-16T20:01:01',
        'www_fi' => 'https://github.com/City-of-Helsinki/drupal-module-helfi-tpr',
        'desc' => 'Description fi',
        'desc_sv' => 'Description sv',
        'latitude' => '60.241573',
        'longitude' => '24.882076',
        'streetview_entrance_url' => 'https://www.google.fi/maps?q=http://www.hel.fi/palvelukartta/kml.aspx?lang%3Dfi%26id%3D2&ll=60.241052,24.882134&spn=0.001012,0.007349&layer=c&cbll=60.24105,24.882129&cbp=12,27.23,,0,0&t=h&panoid=UHkdl6Zbjixy0b8xGN_C3g&z=17',
        'address_postal_full' => 'Pasilankatu 2, Helsinki',
        'street_address' => 'Pasilankatu 2',
        'address_city' => 'Helsinki',
        'address_zip' => '00180',
        'phone' => '040123456, 050123456',
      ],
    ];

    $responses = [
      new Response(200, [], json_encode($units)),
    ];
    foreach ($units as $unit) {
      $responses[] = new Response(200, [], json_encode($unit));
    }
    return $responses;
  }

}
