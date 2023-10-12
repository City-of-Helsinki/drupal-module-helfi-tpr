<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Unit;

use Drupal\helfi_tpr\Field\Connection\Highlight;
use Drupal\helfi_tpr\Field\Connection\Link;
use Drupal\helfi_tpr\Field\Connection\OpeningHour;
use Drupal\helfi_tpr\Field\Connection\OpeningHourObject;
use Drupal\helfi_tpr\Field\Connection\OtherInfo;
use Drupal\helfi_tpr\Field\Connection\PhoneOrEmail;
use Drupal\helfi_tpr\Field\Connection\Price;
use Drupal\helfi_tpr\Field\Connection\TextWithLink;
use Drupal\helfi_tpr\Field\Connection\Topical;

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
   * Tests price.
   *
   * @covers \Drupal\helfi_tpr\Field\Connection\Price::build
   * @covers \Drupal\helfi_tpr\Field\Connection\Price::getFields
   * @covers ::set
   * @covers ::get
   * @covers ::isValidField
   */
  public function testPrice() : void {
    $object = new Price();
    $object->set('name', 'Some information.');
    $this->assertNotEmpty($object->build());

    // Make sure we can override data.
    $object->set('name', 'override');
    $this->assertNotEmpty($object->build());
    $this->assertEquals('override', $object->get('name'));

    $this->assertEquals(['name'], $object->getFields());
  }

  /**
   * Tests other info.
   *
   * @covers \Drupal\helfi_tpr\Field\Connection\OtherInfo::build
   * @covers \Drupal\helfi_tpr\Field\Connection\OtherInfo::getFields
   * @covers ::set
   * @covers ::get
   * @covers ::isValidField
   */
  public function testOtherInfo() : void {
    $object = new OtherInfo();
    $object->set('name', 'Some information.');
    $this->assertNotEmpty($object->build());

    // Make sure we can override data.
    $object->set('name', 'override');
    $this->assertNotEmpty($object->build());
    $this->assertEquals('override', $object->get('name'));

    $this->assertEquals(['name'], $object->getFields());
  }

  /**
   * Tests contacts.
   *
   * @covers \Drupal\helfi_tpr\Field\Connection\PhoneOrEmail::build
   * @covers \Drupal\helfi_tpr\Field\Connection\PhoneOrEmail::getFields
   * @covers ::set
   * @covers ::get
   * @covers ::isValidField
   */
  public function testPhoneOrEmail() : void {
    $object = new PhoneOrEmail();
    $object->set('name', 'Some information.');
    $this->assertNotEmpty($object->build());

    // Make sure we can override data.
    $object->set('name', 'override');
    $this->assertNotEmpty($object->build());

    $this->assertEquals(['name', 'contact_person', 'phone', 'email'], $object->getFields());

    $this->assertNull($object->get('www'));

    $object->set('contact_person', 'John Doe');
    $this->assertNotEmpty($object->build());
    $object->set('phone', '040123456');
    $this->assertNotEmpty($object->build());
    $object->set('email', 'john.doe@example.com');
    $this->assertNotEmpty($object->build());
  }

  /**
   * Tests link.
   *
   * @covers \Drupal\helfi_tpr\Field\Connection\Link::build
   * @covers \Drupal\helfi_tpr\Field\Connection\Link::getFields
   * @covers ::set
   * @covers ::get
   * @covers ::isValidField
   */
  public function testLink() : void {
    $object = new Link();
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
   * Tests topical.
   *
   * @covers \Drupal\helfi_tpr\Field\Connection\Topical::build
   * @covers \Drupal\helfi_tpr\Field\Connection\Topical::getFields
   * @covers ::set
   * @covers ::get
   * @covers ::isValidField
   */
  public function testTopical() : void {
    $object = new Topical();
    $object->set('name', 'Some information');
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
