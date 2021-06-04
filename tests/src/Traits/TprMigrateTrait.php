<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Traits;

use Drupal\helfi_api_base\Fixture\FixtureBase;

/**
 * Provides shared functionality for unit entity tests.
 */
trait TprMigrateTrait {

  /**
   * Gets the migrate fixture.
   *
   * @param string $migrate
   *   The migrate.
   *
   * @return \Drupal\helfi_api_base\Fixture\FixtureBase
   *   The fixture.
   */
  protected function fixture(string $migrate) : FixtureBase {
    return $this->container->get('migration_fixture.' . $migrate);
  }

  /**
   * Runs the 'tpr_unit' migration.
   */
  protected function runUnitMigrate(array $responses = []): void {
    if (empty($responses)) {
      $responses = $this->fixture('tpr_unit')->getMockResponses();
    }
    $this->container->set('http_client', $this->createMockHttpClient($responses));
    $this->executeMigration('tpr_unit');
  }

  /**
   * Runs the 'tpr_service' migration.
   */
  protected function runServiceMigrate(array $responses = []): void {
    if (empty($responses)) {
      $responses = $this->fixture('tpr_service')->getMockResponses();
    }
    $this->container->set('http_client', $this->createMockHttpClient($responses));
    $this->executeMigration('tpr_service');
  }

  /**
   * Runs the 'tpr_errand_service' migration.
   */
  protected function runErrandServiceMigration(array $responses = []) : void {
    if (empty($responses)) {
      $responses = $this->fixture('tpr_errand_service')->getMockResponses();
    }
    $this->container->set('http_client', $this->createMockHttpClient($responses));
    $this->executeMigration('tpr_errand_service');
  }

}
