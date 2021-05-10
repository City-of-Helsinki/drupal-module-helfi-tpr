<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Traits;

use GuzzleHttp\Psr7\Response;

/**
 * Provides shared functionality for unit entity tests.
 */
trait UnitMigrateTrait {

  /**
   * Migrates the tpr unit entities.
   *
   * @param array $units
   *   The units.
   *
   * @throws \Drupal\migrate\MigrateException
   */
  private function runMigrate(array $units): void {
    $responses = [
      new Response(200, [], json_encode($units)),
    ];

    foreach ($units as $unit) {
      $responses[] = new Response(200, [], json_encode($unit));
    }

    $this->container->set('http_client', $this->createMockHttpClient($responses));
    $this->executeMigration('tpr_unit');
  }

}
