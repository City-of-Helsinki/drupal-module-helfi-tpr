<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\Unit;
use GuzzleHttp\Psr7\Response;

/**
 * Tests TPR Unit extras migration.
 *
 * @group helfi_tpr
 */
class UnitExtrasMigrationTest extends MigrationTestBase {

  /**
   * Create the unit extra migration.
   *
   * @return array
   *   List of entities.
   */
  protected function createUnitExtrasMigration(array $units) : array {
    $responses = [];

    foreach ($units as $unit) {
      $responses[] = new Response(200, [], json_encode($unit));
    }

    $this->container->set('http_client', $this->createMockHttpClient($responses));
    $this->executeMigration('tpr_unit_extra');
    return Unit::loadMultiple();
  }

  /**
   * Tests unit extras migration.
   */
  public function testUnitExtraMigration() : void {
    $unit = [
      'id' => 1,
      'name_fi' => 'Name fi 1',
      'name_sv' => 'Name sv 1',
      'name_en' => 'Name en 1',
      'modified_time' => '2015-11-03T12:03:45',
      'accessibility_sentences' => [
        [
          'sentence_group_name' => 'Saattoliikenne',
          'sentence_group_fi' => 'Reitti pääsisäänkäynnille',
          'sentence_group_sv' => 'Rutten till huvudingången',
          'sentence_group_en' => 'The route to the main entrance',
          'sentence_fi' => 'Saattoliikenteen pysähtymispaikka sijaitsee sisäänkäynnin läheisyydessä (etäisyys enintään 5 metriä), josta sisäänkäynnille pääsee siirtymään sujuvasti.',
          'sentence_sv' => 'Hållplatsen för skjutstrafik ligger i närheten av ingången, på en plats varifrån det är lätt att ta sig till trottoaren.',
          'sentence_en' => 'The pick-up and drop-off area is located in the vicinity of the entrance, giving easy access to the pavement.',
        ],
        [
          'sentence_group_name' => 'Kulkureitti pääsisäänkäynnille',
          'sentence_group_fi' => 'Reitti pääsisäänkäynnille',
          'sentence_group_sv' => 'Rutten till huvudingången',
          'sentence_group_en' => 'The route to the main entrance',
          'sentence_fi' => 'Kulkureitti sisäänkäynnille on opastettu, tasainen ja riittävän leveä sekä valaistu ja siinä on liikkumista ohjaavaa pintamateriaalia.',
          'sentence_sv' => 'Rutten till ingången är skyltad, jämn och tillräckligt bred samt belyst och dess ytmaterial ger anvisningar för hur man tar sig fram.',
          'sentence_en' => 'The route to the entrance is guided, smooth and sufficiently wide and illuminated and it uses movement-guiding surface material.',
        ],
      ],
    ];
    $this->createUnitMigration([$unit]);
    $entities = $this->createUnitExtrasMigration([$unit]);
    $this->assertCount(1, $entities);

    foreach (['fi', 'sv', 'en'] as $langcode) {
      /** @var \Drupal\helfi_tpr\Entity\Unit $translation */
      $translation = $entities[1]->getTranslation($langcode);

      $this->assertEquals(2, $translation->get('accessibility_sentences')->count());

      foreach ($unit['accessibility_sentences'] as $delta => $sentence) {
        $this->assertEquals($sentence["sentence_group_$langcode"], $translation->get('accessibility_sentences')->get($delta)->group);
        $this->assertEquals($sentence["sentence_$langcode"], $translation->get('accessibility_sentences')->get($delta)->value);
      }
    }
  }

}
