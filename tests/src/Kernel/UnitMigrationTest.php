<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\Unit;
use Drupal\Tests\helfi_api_base\Kernel\MigrationTestBase;

/**
 * Tests unit migration.
 *
 * @group helfi_tpr
 */
class UnitMigrationTest extends MigrationTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'link',
    'address',
    'text',
    'helfi_tpr',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();

    $this->installEntitySchema('tpr_unit');
    $this->installConfig(['helfi_tpr']);
  }

  /**
   * Test default migrations.
   *
   * @dataProvider migrationsDataProvider
   */
  public function testMigration(string $migrate, int $expectedCount, string $langcode) : void {

    foreach (['tpr_unit', $migrate] as $item) {
      // Override default url with local copy of unit data.
      $config = $this->config('migrate_plus.migration.' . $item);

      $overrides = [
        'urls' => $this->getFixturePath('helfi_tpr', 'unit.json'),
        'data_fetcher_plugin' => 'file',
      ];
      $config->set('source', $overrides + $config->get('source'))
        ->save();

      $this->flushPluginCache();

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
      ['tpr_unit_sv', 6, 'sv'],
      ['tpr_unit_en', 6, 'en'],
    ];
  }

}
