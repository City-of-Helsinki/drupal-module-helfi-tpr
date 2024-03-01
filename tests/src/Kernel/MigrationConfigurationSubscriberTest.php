<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_api_base\Event\MigrationConfigurationEvent;

/**
 * Tests TPR migration configuration subscriber.
 *
 * @group helfi_tpr
 */
class MigrationConfigurationSubscriberTest extends MigrationTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['remote_entity_test'];

  /**
   * Tests that non-tpr migration URLs cannot be changed.
   */
  public function testInvalidMigration() : void {
    $migration = $this->getMigration('dummy_migrate');
    $configuration = $migration->getSourceConfiguration();
    $expectedUrl = $configuration['url'];
    $this
      ->config("helfi_tpr.migration_settings.dummy_migrate")
      ->set('url', "http://localhost/dummy-migrate")
      ->save();

    /** @var \Drupal\helfi_tpr\EventSubscriber\MigrationConfigurationSubscriber $sut */
    $sut = $this->container->get('helfi_tpr.migration_configuration_subscriber');
    $sut->onMigration(new MigrationConfigurationEvent($configuration, $migration));
    $this->assertEquals($expectedUrl, $configuration['url']);
  }

  /**
   * Tests that migration URL can be altered.
   */
  public function testOnMigration() : void {
    $migrations = [
      'tpr_unit',
      'tpr_service',
      'tpr_errand_service',
      'tpr_service_channel',
    ];
    foreach ($migrations as $name) {
      $migration = $this->getMigration($name);
      $configuration = $migration->getSourceConfiguration();
      $configuration['url'] = "http://localhost/$name";
      /** @var \Drupal\helfi_tpr\EventSubscriber\MigrationConfigurationSubscriber $sut */
      $sut = $this->container->get('helfi_tpr.migration_configuration_subscriber');
      $sut->onMigration(new MigrationConfigurationEvent($configuration, $migration));
      $this->assertEquals("http://localhost/$name", $configuration['url']);
      // Make sure configuration can override the URL.
      $this
        ->config("helfi_tpr.migration_settings.$name")
        ->set('url', "http://localhost:8080/$name")
        ->save();
      $sut->onMigration(new MigrationConfigurationEvent($configuration, $migration));
      $this->assertEquals("http://localhost:8080/$name", $configuration['url']);
    }
  }

}
