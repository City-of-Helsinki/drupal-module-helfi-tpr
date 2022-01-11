<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\Tests\helfi_api_base\Kernel\MigrationTestBase as ApiMigrationTestBase;
use Drupal\Tests\helfi_tpr\Traits\TprMigrateTrait;

/**
 * Base class to test TPR migrations.
 */
abstract class MigrationTestBase extends ApiMigrationTestBase {

  use TprMigrateTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'link',
    'address',
    'text',
    'helfi_tpr',
    'media',
    'telephone',
    'menu_link_content',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();

    $entity_types = [
      'menu_link_content',
      'tpr_unit',
      'tpr_service',
      'tpr_errand_service',
      'tpr_service_channel',
      'tpr_ontology_word_details',
    ];

    foreach ($entity_types as $type) {
      $this->installEntitySchema($type);
    }
    $this->installConfig(['helfi_tpr']);
  }

  /**
   * Gets the row hash for given row.
   *
   * @param string $migrationId
   *   The migration id.
   * @param int|string $sourceId
   *   The source id.
   * @param string $language
   *   The language.
   *
   * @return string|null
   *   The migration hash.
   */
  protected function getMigrateMapRowHash(
    string $migrationId,
    int|string $sourceId,
    string $language
  ) : ? string {
    /** @var \Drupal\Core\Database\Connection $database */
    $database = $this->container->get('database');
    $result = $database->select('migrate_map_' . $migrationId, 'm')
      ->fields('m', ['hash'])
      ->condition('sourceid1', $sourceId)
      ->condition('sourceid2', $language)
      ->execute()
      ->fetchObject();

    return $result->hash ?? NULL;
  }

  /**
   * Compares given row hash against previous row hash.
   *
   * @param string $migrationId
   *   The migration id.
   * @param string $expected
   *   The expected hash.
   * @param int|string $sourceId
   *   The source id.
   * @param string $language
   *   The language.
   */
  protected function assertMigrateMapRowHash(
    string $migrationId,
    string $expected,
    int|string $sourceId,
    string $language
  ) {
    $this->assertEquals($expected, $this->getMigrateMapRowHash($migrationId, $sourceId, $language));
  }

}
