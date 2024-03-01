<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Tests override fields.
 *
 * @group helfi_tpr
 */
class OverrideFieldTest extends MigrationTestBase {

  /**
   * Tests name and override name fields.
   *
   * @dataProvider nameData
   */
  public function testName(string $entity_type) : void {
    $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->create(['id' => 1]);
    assert($entity instanceof ContentEntityInterface);
    $entity->set('name', 'Name no override')
      ->save();
    $this->assertEquals('Name no override', $entity->label());

    $entity->set('name_override', 'Name override')
      ->save();
    $this->assertEquals('Name override', $entity->label());
  }

  /**
   * Data provider for nameTest().
   *
   * @return array
   *   The data.
   */
  public function nameData() : array {
    return [
      ['tpr_unit'],
      ['tpr_service'],
      ['tpr_errand_service'],
      ['tpr_service_channel'],
    ];
  }

}
