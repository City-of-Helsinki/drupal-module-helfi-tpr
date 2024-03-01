<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Unit;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\helfi_tpr\Entity\ChannelType;
use Drupal\helfi_tpr\Entity\ChannelTypeCollection;
use Drupal\Tests\UnitTestCase;

/**
 * Tests connection value objects.
 *
 * @coversDefaultClass \Drupal\helfi_tpr\Entity\ChannelTypeCollection
 * @group helfi_tpr
 */
class ChannelTypeCollectionTest extends UnitTestCase {

  /**
   * @covers ::__construct
   * @covers ::createFromArray
   * @covers ::all
   * @covers ::sort
   * @covers \Drupal\helfi_tpr\Entity\ChannelType::__construct
   */
  public function testEmptyCreateFromArray() : void {
    $sut = ChannelTypeCollection::createFromArray();
    $this->assertEquals(ChannelTypeCollection::all(), $sut);
  }

  /**
   * @covers ::__construct
   * @covers ::createFromArray
   * @covers ::all
   * @covers ::sort
   * @covers ::offsetGet
   * @covers \Drupal\helfi_tpr\Entity\ChannelType::__construct
   */
  public function testCreateFromArray() : void {
    $sut = ChannelTypeCollection::createFromArray([
      ChannelType::ESERVICE => ['weight' => 10],
      ChannelType::TELEPHONE => ['weight' => -11],
    ]);
    $this->assertEquals(10, $sut[ChannelType::ESERVICE]->weight);
    $this->assertEquals(ChannelType::ESERVICE, $sut[ChannelType::ESERVICE]->id);
    $this->assertEquals(-11, $sut[ChannelType::TELEPHONE]->weight);
  }

  /**
   * @covers ::__construct
   * @covers ::createFromArray
   * @covers ::all
   * @covers ::sort
   * @covers ::offsetGet
   * @covers ::valid
   * @covers ::sort
   * @covers ::next
   * @covers ::rewind
   * @covers ::current
   * @covers ::key
   * @covers ::count
   * @covers \Drupal\helfi_tpr\Entity\ChannelType::__construct
   */
  public function testDefaultWeight() : void {
    $sut = ChannelTypeCollection::all();
    $weights = array_map(function (ChannelType $type) {
      return $type->weight;
    }, iterator_to_array($sut));

    // Make sure we have unique weight for all items.
    $this->assertEquals(count($sut), count(array_unique($weights)));
  }

  /**
   * Tests array access functions.
   *
   * @covers ::__construct
   * @covers ::createFromArray
   * @covers ::all
   * @covers ::sort
   * @covers ::offsetGet
   * @covers ::offsetSet
   * @covers ::offsetExists
   * @covers ::offsetUnset
   * @covers \Drupal\helfi_tpr\Entity\ChannelType::__construct
   */
  public function testArrayAccess() : void {
    $chat = new ChannelType(ChannelType::CHAT, 1);
    $sut = new ChannelTypeCollection([$chat]);
    $this->assertEquals($chat, $sut[ChannelType::CHAT]);

    $sms = new ChannelType(ChannelType::SMS, 2);
    $sut[ChannelType::SMS] = $sms;
    $this->assertEquals($sms, $sut[ChannelType::SMS]);
    unset($sut[ChannelType::CHAT]);
    $this->assertTrue(empty($sut[ChannelType::CHAT]));
  }

  /**
   * @covers ::__construct
   * @covers ::valid
   * @covers ::sort
   * @covers ::next
   * @covers ::rewind
   * @covers ::current
   * @covers ::key
   * @covers ::count
   * @covers \Drupal\helfi_tpr\Entity\ChannelType::__construct
   */
  public function testIterator() : void {
    $sut = new ChannelTypeCollection([
      new ChannelType(ChannelType::TELEFAX, 1),
      new ChannelType(ChannelType::MAIL, 2),
    ]);

    $index = 0;
    foreach ($sut as $key => $item) {
      $index++;
      $this->assertTrue(is_string($key));
      $this->assertNotNull($item->id);
      $this->assertNotNull($item->weight);
    }
    $this->assertEquals(2, $index);
    $this->assertCount(2, $sut);
  }

  /**
   * @covers ::__construct
   * @covers ::all
   * @covers ::valid
   * @covers ::sort
   * @covers ::next
   * @covers ::rewind
   * @covers ::current
   * @covers ::key
   * @covers ::count
   * @covers \Drupal\helfi_tpr\Entity\ChannelType::__construct
   * @covers \Drupal\helfi_tpr\Entity\ChannelType::label
   * @covers \Drupal\helfi_tpr\Entity\ChannelType::getTypeLabel
   */
  public function testGetLabel() : void {
    $sut = ChannelTypeCollection::all();
    $total = count($sut);
    $this->assertTrue($total > 1);

    $index = 0;
    foreach ($sut as $item) {
      $this->assertInstanceOf(TranslatableMarkup::class, $item->label());
      $this->assertInstanceOf(TranslatableMarkup::class, $item->getTypeLabel());
      $index++;
    }
    $this->assertEquals($total, $index);
  }

}
