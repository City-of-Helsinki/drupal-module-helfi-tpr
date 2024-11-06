<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Unit\Plugin\migrate\process;

use Drupal\Tests\UnitTestCase;
use Drupal\helfi_tpr\Field\Connection\OpeningHour;
use Drupal\helfi_tpr\Field\Connection\OpeningHourObject;
use Drupal\helfi_tpr\Plugin\migrate\process\ArrayElementEquals;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Tests connection value objects.
 *
 * @coversDefaultClass \Drupal\helfi_tpr\Plugin\migrate\process\ArrayElementEquals
 * @group helfi_tpr
 */
class ArrayElementEqualsTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * @covers ::__construct
   * @dataProvider constructorExceptionData
   */
  public function testConstructorException(string $expectedMessage, array $configuration) : void {
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage($expectedMessage);
    new ArrayElementEquals($configuration, 'array_element_equals', []);
  }

  /**
   * Data provider for testConstructorException.
   *
   * @return array[]
   *   The data.
   */
  public function constructorExceptionData() : array {
    return [
      [
        'ArrayElementEquals plugin is missing value configuration',
        [],
      ],
      [
        'ArrayElementEquals plugin is missing key configuration',
        ['value' => '123'],
      ],
    ];
  }

  /**
   * @covers ::__construct
   * @covers ::transform
   * @dataProvider transformData
   */
  public function testTransform(array $configuration, mixed $expectedValue, mixed $value) : void {
    $sut = new ArrayElementEquals($configuration, 'plugin_id', []);
    $this->assertEquals($expectedValue, $sut->transform($value, $this->prophesize(MigrateExecutableInterface::class)->reveal(), new Row([]), 'destination_property'));
  }

  /**
   * Data provider for testTransform.
   *
   * @return array[]
   *   The data.
   */
  public function transformData() : array {
    return [
      // Test non-array value.
      [
        [
          'key' => 'type',
          'value' => OpeningHour::TYPE_NAME,
          'source' => 'connections',
          'plugin' => 'array_element_equals',
        ],
        [],
        '',
      ],
      // Make sure we support non-array 'value' configuration.
      [
        [
          'key' => 'type',
          'value' => OpeningHour::TYPE_NAME,
          'source' => 'connections',
          'plugin' => 'array_element_equals',
        ],
        [
          'type' => OpeningHour::TYPE_NAME,
          'value' => 'Voimassa toistaiseksi',
          'data' => new OpeningHour(),
        ],
        [
          'type' => OpeningHour::TYPE_NAME,
          'value' => 'Voimassa toistaiseksi',
          'data' => new OpeningHour(),
        ],
      ],
      // Make sure we support array 'value' configuration.
      [
        [
          'key' => 'type',
          'value' => [OpeningHourObject::TYPE_NAME, OpeningHour::TYPE_NAME],
          'source' => 'connections',
          'plugin' => 'array_element_equals',
        ],
        [
          'type' => OpeningHour::TYPE_NAME,
          'value' => 'Voimassa toistaiseksi',
          'data' => new OpeningHour(),
        ],
        [
          'type' => OpeningHour::TYPE_NAME,
          'value' => 'Voimassa toistaiseksi',
          'data' => new OpeningHour(),
        ],
      ],
    ];
  }

}
