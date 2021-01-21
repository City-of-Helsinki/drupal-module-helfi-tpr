<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\Unit;
use Drupal\Tests\helfi_api_base\Kernel\MigrationTestBase;
use GuzzleHttp\Psr7\Response;

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
   */
  public function testMigration() : void {
    $units = $this->getFixture('helfi_tpr', 'unit.json');
    $responses = [
      new Response(200, [], $units),
    ];

    foreach (json_decode($units, TRUE) as $id => $unit) {
      $responses[] = new Response(200, [], json_encode($unit));
    }

    $this->container->set('http_client', $this->createMockHttpClient($responses));
    $this->executeMigration('tpr_unit');
    $entities = Unit::loadMultiple();
    $this->assertCount(6, $entities);

    foreach (['en', 'sv'] as $langcode) {
      foreach ($entities as $entity) {
        $this->assertEquals($entity->getTranslation($langcode)->language()->getId(), $langcode);
      }
    }
  }

}
