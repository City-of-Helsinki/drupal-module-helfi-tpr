<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\helfi_tpr\Field\Connection\Connection;
use Drupal\helfi_tpr\Field\Connection\Highlight;
use Drupal\helfi_tpr\Field\Connection\Link;
use Drupal\helfi_tpr\Field\Connection\OpeningHour;
use Drupal\helfi_tpr\Field\Connection\OtherInfo;
use Drupal\helfi_tpr\Field\Connection\PhoneOrEmail;
use Drupal\helfi_tpr\Field\Connection\Price;
use Drupal\helfi_tpr\Field\Connection\Repository;
use Drupal\helfi_tpr\Field\Connection\Subgroup;
use Drupal\helfi_tpr\Field\Connection\Topical;

/**
 * Tests repository value objects.
 *
 * @coversDefaultClass \Drupal\helfi_tpr\Field\Connection\Repository
 * @group helfi_tpr
 */
class ConnectionRepositoryTest extends UnitTestCase {

  /**
   * The connection repository.
   *
   * @var \Drupal\helfi_tpr\Field\Connection\Repository
   */
  protected Repository $repository;

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();

    $this->repository = new Repository();
  }

  /**
   * Tests the repository mapping.
   *
   * @covers ::get
   */
  public function testInvalidRepository() : void {
    $this->assertNull($this->repository->get('invalid'));
  }

  /**
   * Tests the Repository::get() method.
   *
   * @dataProvider getTestData
   * @covers ::get
   */
  public function testGet(string $type) : void {
    $object = $this->repository->get($type);
    $this->assertInstanceOf(Connection::class, $object);
  }

  /**
   * Data provider for testGet().
   *
   * @return array[]
   *   The data.
   */
  public function getTestData() : array {
    return [
      [OpeningHour::TYPE_NAME],
      [Highlight::TYPE_NAME],
      [Link::TYPE_NAME],
      [OtherInfo::TYPE_NAME],
      [Price::TYPE_NAME],
      [PhoneOrEmail::TYPE_NAME],
      [Topical::TYPE_NAME],
      [Subgroup::TYPE_NAME],
    ];
  }

}
