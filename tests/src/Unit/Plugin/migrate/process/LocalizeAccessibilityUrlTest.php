<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Unit\Plugin\migrate\process;

use Drupal\Tests\UnitTestCase;
use Drupal\helfi_tpr\Plugin\migrate\process\LocalizeAccessibilityUrl;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Tests the LocalizeAccessibilityUrl migrate process plugin.
 *
 * @coversDefaultClass \Drupal\helfi_tpr\Plugin\migrate\process\LocalizeAccessibilityUrl
 * @group helfi_tpr
 */
class LocalizeAccessibilityUrlTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * @covers ::transform
   * @dataProvider transformData
   */
  public function testTransform(mixed $input, array $rowSource, string $expected) : void {
    $sut = new LocalizeAccessibilityUrl([], 'localize_accessibility_www', []);
    $executable = $this->prophesize(MigrateExecutableInterface::class)->reveal();
    $row = new Row($rowSource);
    $this->assertSame($expected, $sut->transform($input, $executable, $row, 'accessibility_www'));
  }

  /**
   * Data provider for testTransform.
   *
   * @return array[]
   *   Test cases: [$input, $rowSource, $expected].
   */
  public function transformData() : array {
    $base = 'https://tpr.hel.fi/kapaesteettomyys';

    return [
      // Empty input returns empty string.
      ['' , ['language' => 'fi'], ''],
      [NULL, ['language' => 'en'], ''],

      // Finnish, no change.
      [
        $base . '/app/summary/tpr:1234/',
        ['language' => 'fi'],
        $base . '/app/summary/tpr:1234/',
      ],

      // English, insert langcode 'en' after /app/.
      [
        $base . '/app/summary/tpr:1234/',
        ['language' => 'en'],
        $base . '/app/en/summary/tpr:1234/',
      ],

      // Swedish, insert langcode 'sv' after /app/.
      [
        $base . '/app/summary/tpr:1234/',
        ['language' => 'sv'],
        $base . '/app/sv/summary/tpr:1234/',
      ],

      // Unknown language: fallback like 'fi' (no language segment).
      [
        $base . '/app/summary/tpr:1234/',
        ['language' => 'it'],
        $base . '/app/summary/tpr:1234/',
      ],

      // Url with no /app/ segment, no change.
      [
        'https://www.test.hel.ninja/fi/node/1234',
        ['language' => 'fi'],
        'https://www.test.hel.ninja/fi/node/1234',
      ],

      // Input is trimmed before processing.
      [
        '  ' . $base . '/app/summary/tpr:1234  ',
        ['language' => 'en'],
        $base . '/app/en/summary/tpr:1234',
      ],
    ];
  }

}
