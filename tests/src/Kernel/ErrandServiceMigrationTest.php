<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\ErrandService;

/**
 * Tests TPR Errand service migration.
 *
 * @group helfi_tpr
 */
class ErrandServiceMigrationTest extends MigrationTestBase {

  /**
   * Tests that default values are not overridden by migrate.
   */
  public function testDefaultErrandServiceValues() : void {
    $this->runErrandServiceMigration();
    $entities = ErrandService::loadMultiple();

    // Update translation author and status fields.
    $translation = $entities[1]->getTranslation('sv');

    $this->assertEquals('1', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('0', $translation->get('content_translation_status')->value);

    $translation->set('content_translation_uid', '0')
      ->set('content_translation_status', TRUE)
      ->save();

    // Re-run migrate and make sure author and status fields won't get updated.
    $this->runErrandServiceMigration();
    $entities = ErrandService::loadMultiple();
    $translation = $entities[1]->getTranslation('sv');

    $this->assertEquals('0', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('1', $translation->get('content_translation_status')->value);
  }

  /**
   * Tests errand service migration.
   */
  public function testErrandServiceMigration() : void {
    $this->runErrandServiceMigration();
    $entities = ErrandService::loadMultiple();
    $this->assertCount(3, $entities);

    $expectedMapHashes = [];

    foreach (['fi', 'sv', 'en'] as $langcode) {
      foreach ($entities as $entity) {
        $translation = $entity->getTranslation($langcode);

        $expectedMapHashes[$langcode] = [
          'id' => $translation->id(),
          'expected_hash' => $this->getMigrateMapRowHash('tpr_errand_service', $translation->id(), $langcode),
        ];

        $this->assertEquals($langcode, $translation->language()->getId());

        /** @var \Drupal\helfi_tpr\Fixture\ErrandService $fixture */
        $fixture = $this->fixture('tpr_errand_service');
        foreach ($fixture->getFields() as $field) {
          $this->assertEquals(
            sprintf('%s %s %s', $field, $langcode, $translation->id()),
            trim($translation->get($field)->value)
          );
        }

        for ($i = 0; $i < 3; $i++) {
          /** @var \Drupal\link\Plugin\Field\FieldType\LinkItem $link */
          $link = $translation->get('links')->get($i);
          $this->assertEquals(sprintf('%s:%s link title %s', $i, $langcode, $translation->id()), $link->title);
          $this->assertEquals(sprintf('https://localhost/%s/%s/%s', $i, $langcode, $translation->id()), trim($link->uri));
        }
      }
    }

    // Re-run migrate and make sure migrate map hash doesn't change.
    $this->runErrandServiceMigration();

    foreach ($expectedMapHashes as $langcode => $data) {
      $this->assertMigrateMapRowHash('tpr_errand_service', $data['expected_hash'], $data['id'], $langcode);
    }
  }

}
