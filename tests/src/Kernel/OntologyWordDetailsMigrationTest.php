<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\OntologyWordDetails;

/**
 * Tests TPR Ontology word details migration.
 *
 * @group helfi_tpr
 */
class OntologyWordDetailsMigrationTest extends MigrationTestBase {

  /**
   * Tests ontology word details migration.
   */
  public function testOntologyWordDetailsMigration() : void {
    $this->runOntologyWordDetailsMigrate();
    $entities = OntologyWordDetails::loadMultiple();
    $this->assertCount(2, $entities);

    foreach ($this->expectedOntologyWordDetailsData() as $langcode => $expected) {
      /** @var \Drupal\helfi_tpr\Entity\Unit $translation */
      $translation = $entities['157_30855']->getTranslation($langcode);

      $this->assertEquals($expected['id'], $translation->id());
      $this->assertEquals($expected['name'], $translation->label());
      $this->assertEquals($expected['ontologyword_id'], $translation->get('ontologyword_id')->value);
      $this->assertEquals($expected['unit_id'], $translation->get('unit_id')->value);
      $this->assertEquals(1, $translation->get('detail_items')->count());
    }
  }

  /**
   * The data provider for ontology word details migration.
   *
   * @return array
   *   The data.
   */
  public function expectedOntologyWordDetailsData() : array {
    return [
      'en' => [
        'id' => '157_30855',
        'name' => 'special educational mission upper secondary schools',
        'ontologyword_id' => '157',
        'unit_id' => '30855',
      ],
      'fi' => [
        'id' => '157_30855',
        'name' => 'erityistehtävän mukaiset lukiot',
        'ontologyword_id' => '157',
        'unit_id' => '30855',
      ],
      'sv' => [
        'id' => '157_30855',
        'name' => 'gymnasier enligt specialuppgift',
        'ontologyword_id' => '157',
        'unit_id' => '30855',
      ],
    ];
  }

}
