<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\Channel;

/**
 * Tests TPR Service Channel entities.
 *
 * @coversDefaultClass \Drupal\helfi_tpr\Entity\Channel
 * @group helfi_tpr
 */
class ChannelEntityTest extends MigrationTestBase {

  /**
   * Gets the TPR Service Channel entity.
   *
   * @param int $id
   *   The id.
   *
   * @return \Drupal\helfi_tpr\Entity\Channel
   *   The entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function getEntity(int $id) : Channel {
    $entity = Channel::create([
      'id' => $id,
      'name' => 'TPR Service Channel ' . $id,
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
    $this->assertEquals(NULL, Channel::load(1));
  }

}
