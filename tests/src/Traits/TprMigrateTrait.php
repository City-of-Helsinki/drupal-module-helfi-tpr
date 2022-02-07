<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Traits;

use Drupal\helfi_api_base\Fixture\FixtureBase;

/**
 * Provides shared functionality for TPR entity tests.
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

  /**
   * Runs the `tpr_service_channel' migration.
   */
  protected function runServiceChannelMigration(array $responses = []) : void {
    // Service channel migration uses the same data as errand service.
    if (empty($responses)) {
      $responses = $this->fixture('tpr_errand_service')->getMockResponses();
    }
    $this->container->set('http_client', $this->createMockHttpClient($responses));
    $this->executeMigration('tpr_service_channel');
  }

  /**
   * Runs the 'tpr_ontology_word_details' migration.
   *
   * @param array $responses
   *   The mock responses.
   */
  protected function runOntologyWordDetailsMigrate(array $responses = []): void {
    if (empty($responses)) {
      $responses = $this->fixture('tpr_ontology_word_details')->getMockResponses();
    }
    $this->container->set('http_client', $this->createMockHttpClient($responses));
    $this->executeMigration('tpr_ontology_word_details');
  }

}
