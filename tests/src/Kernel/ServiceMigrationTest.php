<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\Unit;

/**
 * Tests TPR Service migration.
 *
 * @group helfi_tpr
 */
class ServiceMigrationTest extends MigrationTestBase {

  /**
   * Tests service migration.
   */
  public function testServiceMigration() : void {
    // Services has soft dependency on unit migration.
    $this->createUnitMigration();
    $entities = $this->createServiceMigration();
    $this->assertCount(3, $entities);

    // Make sure unit_ids are mapped properly.
    $this->assertTrue(Unit::load(1)->hasService($entities[1]));

    foreach (['fi', 'sv', 'en'] as $langcode) {
      /** @var \Drupal\helfi_tpr\Entity\Service $entity */
      foreach ($entities as $entity) {
        /** @var \Drupal\helfi_tpr\Entity\Service $translation */
        $translation = $entity->getTranslation($langcode);

        $this->assertEquals($langcode, $translation->language()->getId());
        $this->assertEquals(sprintf('Service %s %s', $langcode, $translation->id()), $translation->label());
        $this->assertEquals('1', $translation->get('content_translation_uid')->target_id);

        for ($i = 0; $i < 2; $i++) {
          $this->assertEquals(sprintf('%s: %s link title %s', $i, $langcode, $translation->id()), $translation->get('links')->get($i)->title);
        }

        $this->assertEquals(sprintf('Description short %s %s', $langcode, $translation->id()), $translation->get('description')->summary);
        $this->assertEquals(sprintf('Description long %s %s', $langcode, $translation->id()), $translation->get('description')->value);

        foreach ([123, 456] as $key => $id) {
          $this->assertEquals($id, $translation->get('errand_services')->get($key)->target_id);
        }
      }
    }
  }

}
