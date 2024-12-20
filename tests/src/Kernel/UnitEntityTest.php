<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\Unit;

/**
 * Tests TPR Unit entities.
 *
 * @coversDefaultClass \Drupal\helfi_tpr\Entity\Unit
 * @group helfi_tpr
 */
class UnitEntityTest extends MigrationTestBase {

  /**
   * Gets the TPR Unit entity.
   *
   * @param int $id
   *   The id.
   *
   * @return \Drupal\helfi_tpr\Entity\Unit
   *   The entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function getEntity(int $id) : Unit {
    $entity = Unit::create([
      'id' => $id,
      'name' => 'TPR Unit ' . $id,
    ]);
    $entity->save();

    return $entity;
  }

  /**
   * Tests entity deletion.
   *
   * @covers ::create
   * @covers ::delete
   * @covers ::save
   */
  public function testEntityDeletion() : void {
    $entity = $this->getEntity(1);

    $entity->delete();
    $this->assertEquals(NULL, Unit::load(1));
  }

}
