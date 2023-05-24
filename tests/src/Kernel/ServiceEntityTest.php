<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\Service;

/**
 * Tests TPR Service entities.
 *
 * @coversDefaultClass \Drupal\helfi_tpr\Entity\Service
 * @group helfi_tpr
 */
class ServiceEntityTest extends MigrationTestBase {

  /**
   * Gets the TPR Service entity.
   *
   * @param int $id
   *   The id.
   *
   * @return \Drupal\helfi_tpr\Entity\Service
   *   The entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function getEntity(int $id) : Service {
    $entity = Service::create([
      'id' => $id,
      'name' => 'TPR Service ' . $id,
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

    // Test that the entity is not deleted.
    // See Drupal\helfi_tpr\Entity\TprEntityBase::delete() for more
    // information.
    $entity->delete();
    $this->assertNotEquals(NULL, Service::load(1));
  }

}
