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
