<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\Unit;
use Drupal\helfi_tpr\Field\Connection\Highlight;
use Drupal\helfi_tpr\Field\Connection\Link;
use Drupal\helfi_tpr\Field\Connection\OpeningHour;
use Drupal\helfi_tpr\Field\Connection\OpeningHourObject;
use Drupal\helfi_tpr\Field\Connection\OtherInfo;
use Drupal\helfi_tpr\Field\Connection\PhoneOrEmail;
use Drupal\helfi_tpr\Field\Connection\Price;
use Drupal\helfi_tpr\Field\Connection\Subgroup;
use Drupal\helfi_tpr\Field\Connection\Topical;

/**
 * Tests TPR Unit migration.
 *
 * @group helfi_tpr
 */
class UnitMigrationTest extends MigrationTestBase {

  /**
   * Tests unit migration.
   */
  public function testUnitMigration() : void {
    $this->runUnitMigrate();
    $entities = Unit::loadMultiple();
    $this->assertCount(5, $entities);

    $expectedMapHashes = [];

    foreach ($this->expectedUnitData() as $langcode => $expected) {
      /** @var \Drupal\helfi_tpr\Entity\Unit $translation */
      $translation = $entities[1]->getTranslation($langcode);

      $expectedMapHashes[$langcode] = [
        'id' => $translation->id(),
        'expected_hash' => $this->getMigrateMapRowHash('tpr_unit', $translation->id(), $langcode),
      ];

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

      $this->assertEquals(2, $translation->get('accessibility_sentences')->count());

      $provided_languages = [];

      foreach ($translation->get('provided_languages')->getValue() as $value) {
        $provided_languages[] = $value['value'];
      }

      $this->assertEquals(['fi', 'sv', 'en'], $provided_languages);

      for ($i = 0; $i < 2; $i++) {
        $delta = $i + 1;
        $this->assertEquals("Group $langcode $delta", $translation->get('accessibility_sentences')->get($i)->group);
        $this->assertEquals("Sentence $langcode $delta", $translation->get('accessibility_sentences')->get($i)->value);
        $this->assertEquals("open $langcode $delta", $translation->get('opening_hours')->get($i)->value);
        $this->assertEquals("phone or email $langcode $delta", $translation->get('contacts')->get($i)->value);
        $this->assertEquals("link $langcode $delta", $translation->get('links')->get($i)->value);
        $this->assertEquals("price $langcode $delta", $translation->get('price_info')->get($i)->value);
        $this->assertEquals("other info $langcode $delta", $translation->get('other_info')->get($i)->value);
        $this->assertEquals("topical $langcode $delta", $translation->get('topical')->get($i)->value);
        $this->assertEquals("subgroup $langcode $delta", $translation->get('subgroup')->get($i)->value);
      }

      $opening_hour = $translation->get('opening_hours')->get(1)->data;
      $this->assertInstanceOf(OpeningHour::class, $opening_hour);
      $opening_hour_object = $translation->get('opening_hours')->get(2)->data;
      $this->assertInstanceOf(OpeningHourObject::class, $opening_hour_object);

      $this->assertEquals("https://localhost/$langcode", $opening_hour->get('www'));
      $highlight = $translation->get('highlights')->get(0)->data;
      $this->assertInstanceOf(Highlight::class, $highlight);
      $this->assertEquals("highlight $langcode 1", $highlight->get('name'));

      $contacts = $translation->get('contacts')->get(1)->data;
      $this->assertInstanceOf(PhoneOrEmail::class, $contacts);
      $this->assertEquals("contact person name", $contacts->get('contact_person'));
      $this->assertEquals("040654321", $contacts->get('phone'));

      $links = $translation->get('links')->get(0)->data;
      $this->assertInstanceOf(Link::class, $links);
      $this->assertEquals("https://localhost/$langcode", $links->get('www'));

      $price_info = $translation->get('price_info')->get(0)->data;
      $this->assertInstanceOf(Price::class, $price_info);

      $other_info = $translation->get('other_info')->get(0)->data;
      $this->assertInstanceOf(OtherInfo::class, $other_info);

      $topical = $translation->get('topical')->get(0)->data;
      $this->assertInstanceOf(Topical::class, $topical);
      $this->assertEquals("https://localhost/$langcode", $links->get('www'));

      $subgroup = $translation->get('subgroup')->get(1)->data;
      $this->assertInstanceOf(Subgroup::class, $subgroup);
      $this->assertEquals("subgroup contact person name", $subgroup->get('contact_person'));
      $this->assertEquals("0406543210", $subgroup->get('phone'));
      $this->assertEquals("subgroup@example.com", $subgroup->get('email'));
    }

    // Re-run migrate and make sure migrate map hash doesn't change.
    $this->runUnitMigrate();

    foreach ($expectedMapHashes as $langcode => $data) {
      $this->assertMigrateMapRowHash('tpr_unit', $data['expected_hash'], $data['id'], $langcode);
    }
  }

  /**
   * Tests that default values are not overridden by migrate.
   */
  public function testDefaultUnitValues() : void {
    $this->runUnitMigrate();
    $entities = Unit::loadMultiple();

    // Update translation author and status fields.
    $translation = $entities[1]->getTranslation('sv');

    $this->assertEquals('1', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('0', $translation->get('content_translation_status')->value);

    $translation->set('content_translation_uid', '0')
      ->set('content_translation_status', TRUE)
      ->save();

    // Re-run migrate and make sure author and status fields won't get updated.
    $this->runUnitMigrate();
    $entities = Unit::loadMultiple();
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
      'en' => [
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
      'fi' => [
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
      'sv' => [
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
    ];
  }

}
