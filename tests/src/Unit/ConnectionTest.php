<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Unit;

use Drupal\helfi_tpr\Field\Connection\Highlight;
use Drupal\helfi_tpr\Field\Connection\OpeningHour;
use Drupal\helfi_tpr\Field\Connection\OpeningHourObject;
use Drupal\helfi_tpr\Field\Connection\TextWithLink;
use Drupal\Tests\UnitTestCase;

/**
 * Tests connection value objects.
 *
 * @coversDefaultClass \Drupal\helfi_tpr\Field\Connection\Connection
 * @group helfi_tpr
 */
class ConnectionTest extends UnitTestCase {

  /**
   * Tests opening hours.
   *
   * @covers \Drupal\helfi_tpr\Field\Connection\TextWithLink::build
   * @covers \Drupal\helfi_tpr\Field\Connection\TextWithLink::getFields
   * @covers ::set
   * @covers ::get
   * @covers ::isValidField
   * @dataProvider openingHourData
   */
  public function testOpeningHours(TextWithLink $object) : void {
    $object->set('name', 'mon-wed 10-19');
    $this->assertNotEmpty($object->build());

    // Make sure we can override data.
    $object->set('name', 'override');
    $this->assertNotEmpty($object->build());

    $this->assertEquals(['name', 'www'], $object->getFields());

    $this->assertNull($object->get('www'));

    $object->set('www', 'https://localhost');
    $this->assertNotEmpty($object->build());
  }

  /**
   * Tests highlights.
   *
   * @covers \Drupal\helfi_tpr\Field\Connection\Highlight::build
   * @covers \Drupal\helfi_tpr\Field\Connection\Highlight::getFields
   * @covers ::set
   * @covers ::get
   * @covers ::isValidField
   */
  public function testHighlights() : void {
    $object = new Highlight();
    $object->set('name', 'Some information.');
    $this->assertNotEmpty($object->build());

    // Make sure we can override data.
    $object->set('name', 'override');
    $this->assertNotEmpty($object->build());
    $this->assertEquals('override', $object->get('name'));

    $this->assertEquals(['name'], $object->getFields());
  }

  /**
   * Tests invalid field name.
   *
   * @covers ::set
   * @covers ::isValidField
   * @covers \Drupal\helfi_tpr\Field\Connection\TextWithLink::getFields
   * @dataProvider openingHourData
   */
  public function testInvalidFieldName(TextWithLink $object) : void {
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('Field "invalid_field" is not valid.');
    $object->set('invalid_field', 'value');
  }

  /**
   * Data provider opening hour tests.
   *
   * @return array
   *   The data.
   */
  public function openingHourData() : array {
    return [
      [new OpeningHour()],
      [new OpeningHourObject()],
    ];
  }

  /**
   * Tests invalid data type.
   *
   * @dataProvider invalidFieldValueData
   * @covers \Drupal\helfi_tpr\Field\Connection\TextWithLink::getFields
   * @covers ::set
   * @covers ::isValidField
   */
  public function testInvalidFieldValue($value) : void {
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('Only scalar or null values allowed for "name".');
    $object = new OpeningHour();
    $object->set('name', $value);
  }

  /**
   * Data provider for testInvalidFieldValue().
   *
   * @return array
   *   The data.
   */
  public function invalidFieldValueData() : array {
    return [
      [[]],
      [new \stdClass()],
    ];
  }

  /**
   * Tests valid values.
   *
   * @dataProvider validFieldValueData
   * @covers \Drupal\helfi_tpr\Field\Connection\TextWithLink::getFields
   * @covers ::set
   * @covers ::get
   * @covers ::isValidField
   */
  public function testValidFieldValue($value) : void {
    $object = new OpeningHour();
    $object->set('name', $value);
    $this->assertEquals($value, $object->get('name'));
  }

  /**
   * Data provider for testScalarFieldValue().
   *
   * @return array
   *   The data.
   */
  public function validFieldValueData() : array {
    return [
      [1.234],
      [-1],
      ['string'],
      [1],
      [TRUE],
      [FALSE],
      [NULL],
    ];
  }

}
