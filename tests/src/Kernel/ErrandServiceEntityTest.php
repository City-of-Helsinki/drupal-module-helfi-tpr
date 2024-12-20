<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\ErrandService;

/**
 * Tests TPR Errand Service entities.
 *
 * @coversDefaultClass \Drupal\helfi_tpr\Entity\ErrandService
 * @group helfi_tpr
 */
class ErrandServiceEntityTest extends MigrationTestBase {

  /**
   * Gets the TPR Errand Service entity.
   *
   * @param int $id
   *   The id.
   *
   * @return \Drupal\helfi_tpr\Entity\ErrandService
   *   The entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function getEntity(int $id) : ErrandService {
    $entity = ErrandService::create([
      'id' => $id,
      'name' => 'TPR Errand Service ' . $id,
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
    $this->assertEquals(NULL, ErrandService::load(1));
  }

}
