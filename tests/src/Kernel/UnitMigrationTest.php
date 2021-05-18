<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

/**
 * Tests TPR Unit migration.
 *
 * @group helfi_tpr
 */
class UnitMigrationTest extends MigrationTestBase {

  /**
   * Gets the unit data.
   *
   * @return array[]
   *   The unit data.
   */
  protected function getUnitData() : array {
    return [
      [
        'id' => 1,
        'name_fi' => 'Name fi 1',
        'name_sv' => 'Name sv 1',
        'name_en' => 'Name en 1',
        'latitude' => '60.19',
        'longitude' => '24.76',
        'street_address_fi' => 'Address fi 1',
        'street_address_sv' => 'Address sv 1',
        'address_zip' => '02180',
        'address_city_fi' => 'Espoo fi 1',
        'address_city_sv' => 'Espoo sv 1',
        'address_city_en' => 'Espoo en 1',
        'phone' => '+3581234',
        'call_charge_info_fi' => 'pvm fi 1',
        'call_charge_info_en' => 'pvm en 1',
        'call_charge_info_sv' => 'pvm sv 1',
        'www_fi' => 'https://localhost/fi/1',
        'www_sv' => 'https://localhost/sv/1',
        'www_en' => 'https://localhost/en/1',
        'created_time' => '2015-11-03T12:03:45',
        'modified_time' => '2015-11-03T12:03:45',
      ],
    ];
  }

  /**
   * Tests unit migration.
   *
   * @dataProvider expectedUnitData
   */
  public function testUnitMigration(string $langcode, array $expected) : void {
    $entities = $this->createUnitMigration($this->getUnitData());
    $this->assertCount(1, $entities);

    foreach ($entities as $entity) {
      /** @var \Drupal\helfi_tpr\Entity\Unit $translation */
      $translation = $entity->getTranslation($langcode);

      $this->assertEquals($expected['id'], $translation->id());
      $this->assertEquals($expected['name'], $translation->label());
      $this->assertEquals($expected['latitude'], $translation->get('latitude')->value);
      $this->assertEquals($expected['longitude'], $translation->get('longitude')->value);
      $this->assertEquals($expected['street_address'], $translation->get('address')->address_line1);
      $this->assertEquals($expected['address_zip'], $translation->get('address')->postal_code);
      $this->assertEquals($expected['address_city'], $translation->get('address')->locality);
      $this->assertEquals('FI', $translation->get('address')->country_code);
      $this->assertEquals($expected['phone'], $translation->get('phone')->value);
      $this->assertEquals($expected['call_charge_info'], $translation->get('call_charge_info')->value);
      $this->assertEquals($expected['www'], $translation->get('www')->uri);
    }
  }

  /**
   * Tests that default values are not overridden by migrate.
   */
  public function testDefaultUnitValues() : void {
    $entities = $this->createUnitMigration($this->getUnitData());

    // Update translation author and status fields.
    $translation = $entities[1]->getTranslation('sv');

    $this->assertEquals('1', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('0', $translation->get('content_translation_status')->value);

    $translation->set('content_translation_uid', '0')
      ->set('content_translation_status', TRUE)
      ->save();

    // Re-run migrate and make sure author and status fields won't get updated.
    $entities = $this->createUnitMigration($this->getUnitData());
    $translation = $entities[1]->getTranslation('sv');

    $this->assertEquals('0', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('1', $translation->get('content_translation_status')->value);
  }

  /**
   * The data provider for unit migration.
   *
   * @return array
   *   The data.
   */
  public function expectedUnitData() : array {
    return [
      [
        'en',
        [
          'id' => 1,
          'name' => 'Name en 1',
          'latitude' => '60.19',
          'longitude' => '24.76',
          'street_address' => NULL,
          'address_zip' => '02180',
          'address_city' => 'Espoo en 1',
          'phone' => '+3581234',
          'call_charge_info' => 'pvm en 1',
          'www' => 'https://localhost/en/1',
        ],
      ],
      [
        'fi',
        [
          'id' => 1,
          'name' => 'Name fi 1',
          'latitude' => '60.19',
          'longitude' => '24.76',
          'street_address' => 'Address fi 1',
          'address_zip' => '02180',
          'address_city' => 'Espoo fi 1',
          'phone' => '+3581234',
          'call_charge_info' => 'pvm fi 1',
          'www' => 'https://localhost/fi/1',
        ],
      ],
      [
        'sv',
        [
          'id' => 1,
          'name' => 'Name sv 1',
          'latitude' => '60.19',
          'longitude' => '24.76',
          'street_address' => 'Address sv 1',
          'address_zip' => '02180',
          'address_city' => 'Espoo sv 1',
          'phone' => '+3581234',
          'call_charge_info' => 'pvm sv 1',
          'www' => 'https://localhost/sv/1',
        ],
      ],
    ];
  }

}
