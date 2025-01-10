<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\OntologyWordDetails;

/**
 * Tests TPR Ontology word details entities.
 *
 * @coversDefaultClass \Drupal\helfi_tpr\Entity\OntologyWordDetails
 * @group helfi_tpr
 */
class OntologyWordDetailsTest extends MigrationTestBase {

  /**
   * Gets the TPR Ontology word details entity.
   *
   * @param string $id
   *   The id.
   *
   * @return \Drupal\helfi_tpr\Entity\OntologyWordDetails
   *   The entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function getEntity(string $id) : OntologyWordDetails {
    $entity = OntologyWordDetails::create([
      'id' => $id,
      'name' => 'TPR Ontology word details ' . $id,
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
    $entity = $this->getEntity('1_1');

    $entity->delete();
    $this->assertNotEquals(NULL, OntologyWordDetails::load('1_1'));
  }

}
