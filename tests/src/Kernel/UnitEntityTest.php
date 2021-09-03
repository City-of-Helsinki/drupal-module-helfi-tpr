<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\helfi_tpr\Entity\Unit;

/**
 * Tests TPR Unit entities.
 *
 * @group helfi_tpr
 */
class UnitEntityTest extends MigrationTestBase {

  /**
   * Gets the TPR Unit entity.
   *
   * @param int $id
   *   The id.
   *
   * @return Unit
   *   The entity.
   *
   * @throws EntityStorageException
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
   */
  public function testEntityDeletion() : void {
    $entity = $this->getEntity(1);

    // Test that the entity is not deleted.
    // See Drupal\helfi_tpr\Entity\TprEntityBase::delete() for more
    // information.
    $entity->delete();
    $this->assertNotEquals(NULL, Unit::load(1));
  }

}
