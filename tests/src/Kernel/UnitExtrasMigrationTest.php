<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\Unit;
use Drupal\helfi_tpr\Field\Connection\OpeningHour;

/**
 * Tests TPR Unit extras migration.
 *
 * @group helfi_tpr
 */
class UnitExtrasMigrationTest extends MigrationTestBase {

  /**
   * Tests unit extras migration.
   */
  public function testUnitExtraMigration() : void {
    $this->runUnitMigrate();
    $this->runUnitExtrasMigration();
    $entities = Unit::loadMultiple();
    $this->assertCount(1, $entities);

    foreach (['fi', 'sv', 'en'] as $langcode) {
      /** @var \Drupal\helfi_tpr\Entity\Unit $translation */
      $translation = $entities[1]->getTranslation($langcode);

      $this->assertEquals(2, $translation->get('accessibility_sentences')->count());

      for ($i = 0; $i < 2; $i++) {
        $delta = $i + 1;
        $this->assertEquals("Group $langcode $delta", $translation->get('accessibility_sentences')->get($i)->group);
        $this->assertEquals("Sentence $langcode $delta", $translation->get('accessibility_sentences')->get($i)->value);
        $this->assertEquals("open $langcode $delta", $translation->get('opening_hours')->get($i)->value);
      }

      $opening_hour = $translation->get('opening_hours')->get(1)->data;
      $this->assertInstanceOf(OpeningHour::class, $opening_hour);
      $this->assertEquals("https://localhost/$langcode", $opening_hour->get('www'));
    }
  }

}
