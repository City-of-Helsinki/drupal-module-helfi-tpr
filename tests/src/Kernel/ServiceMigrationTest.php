<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\Service;
use Drupal\helfi_tpr\Entity\Unit;

/**
 * Tests TPR Service migration.
 *
 * @group helfi_tpr
 */
class ServiceMigrationTest extends MigrationTestBase {

  /**
   * Tests that default values are not overridden by migrate.
   */
  public function testDefaultServiceValues() : void {
    $this->runServiceMigrate();
    $entities = Service::loadMultiple();

    // Update translation author and status fields.
    $translation = $entities[1]->getTranslation('sv');

    $this->assertEquals('1', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('0', $translation->get('content_translation_status')->value);

    $translation->set('content_translation_uid', '0')
      ->set('content_translation_status', TRUE)
      ->save();

    // Re-run migrate and make sure author and status fields won't get updated.
    $this->runServiceMigrate();
    $entities = Service::loadMultiple();
    $translation = $entities[1]->getTranslation('sv');

    $this->assertEquals('0', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('1', $translation->get('content_translation_status')->value);
  }

  /**
   * Tests service migration.
   */
  public function testServiceMigration() : void {
    // Service migration has a soft dependency on unit migration.
    $this->runUnitMigrate();
    $this->runServiceMigrate();
    $entities = Service::loadMultiple();
    $this->assertCount(6, $entities);

    // We have fixture data for functional tests as well, and they are not
    // easily testable. Test only first three entities.
    $entities = array_slice($entities, 0, 3, TRUE);

    $expectedMapHashes = [];

    // Make sure unit_ids are mapped properly.
    $this->assertTrue(Unit::load(1)->hasService($entities[1]));

    foreach (['fi', 'sv', 'en'] as $langcode) {
      /** @var \Drupal\helfi_tpr\Entity\Service $entity */
      foreach ($entities as $entity) {
        /** @var \Drupal\helfi_tpr\Entity\Service $translation */
        $translation = $entity->getTranslation($langcode);

        $expectedMapHashes[$langcode] = [
          'id' => $translation->id(),
          'expected_hash' => $this->getMigrateMapRowHash('tpr_service', $translation->id(), $langcode),
        ];

        $this->assertEquals($langcode, $translation->language()->getId());
        $this->assertEquals(sprintf('Service %s %s', $langcode, $translation->id()), $translation->label());

        for ($i = 0; $i < 2; $i++) {
          $this->assertEquals(sprintf('%s: %s link title %s', $i, $langcode, $translation->id()), $translation->get('links')->get($i)->title);
          $this->assertEquals(sprintf('https://localhost/%s/%s/%s', $i, $langcode, $translation->id()), $translation->get('links')->get($i)->uri);
        }

        $this->assertEquals(sprintf('Description short %s %s', $langcode, $translation->id()), $translation->get('description')->summary);
        $this->assertEquals(sprintf('Description long %s %s', $langcode, $translation->id()), $translation->get('description')->value);

        $this->assertEquals(sprintf('Name synonyms %s %s', $langcode, $translation->id()), $translation->get('name_synonyms')->value);

        $this->assertEquals(10554, $translation->get('service_id')->value);
        $has_unit = !empty($translation->get('has_unit')->value);
        if ($translation->id() == 2) {
          $this->assertTrue($has_unit);
        }
        else {
          $this->assertFalse($has_unit);
        }

        foreach ([123, 456] as $key => $id) {
          $this->assertEquals($id, $translation->get('errand_services')->get($key)->target_id);
        }
      }
    }

    // Re-run migrate and make sure migrate map hash doesn't change.
    $this->runServiceMigrate();

    foreach ($expectedMapHashes as $langcode => $data) {
      $this->assertMigrateMapRowHash('tpr_service', $data['expected_hash'], $data['id'], $langcode);
    }
  }

}
