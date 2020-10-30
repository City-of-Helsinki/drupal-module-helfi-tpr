<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_trp\Kernel;

use Drupal\helfi_trp\Entity\Unit;
use Drupal\Tests\helfi_api_base\Kernel\MigrationTestBase;

/**
 * Tests unit migration.
 *
 * @group helfi_trp
 */
class UnitMigrationTest extends MigrationTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'link',
    'address',
    'text',
    'helfi_trp',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->installEntitySchema('trp_unit');
    $this->installConfig(['helfi_trp']);
  }

  /**
   * Test default migrations.
   *
   * @dataProvider migrationsDataProvider
   */
  public function testMigration(string $migrate, int $expectedCount, string $langcode) : void {

    foreach (['trp_unit', $migrate] as $item) {
      // Override default url with local copy of unit data.
      $config = $this->config('migrate_plus.migration.' . $item);

      $overrides = [
        'urls' => $this->getFixturePath('helfi_trp', 'unit.json'),
        'data_fetcher_plugin' => 'file',
      ];
      $config->set('source', $overrides + $config->get('source'))
        ->save();

      \Drupal::service('plugin.cache_clearer')->clearCachedDefinitions();

      $this->executeMigration($item);
    }
    $entities = Unit::loadMultiple();
    $this->assertCount($expectedCount, $entities);

    foreach ($entities as $entity) {
      $this->assertEqual($entity->getTranslation($langcode)->language()->getId(), $langcode);
    }
  }

  /**
   * Gets the migration data.
   *
   * @return array[]
   *   The migration.
   */
  public function migrationsDataProvider() : array {
    return [
      ['trp_unit_sv', 6, 'sv'],
      ['trp_unit_en', 6, 'en'],
    ];
  }

}
