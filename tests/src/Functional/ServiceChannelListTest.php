<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Tests\helfi_tpr\Traits\TprMigrateTrait;

/**
 * Tests Service Channel entity's list functionality.
 *
 * @group helfi_tpr
 */
class ServiceChannelListTest extends ListTestBase {

  use TprMigrateTrait;

  /**
   * {@inheritdoc}
   */
  protected string $entityType = 'tpr_service_channel';

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();
    $this->listPermissions = [
      'access remote entities overview',
      'administer remote entities',
      'edit remote entities',
    ];
    $this->adminListPath = '/admin/content/integrations/tpr-service-channel';
  }

  /**
   * Tests list view permissions, and viewing, updating, and publishing.
   */
  public function testList() : void {
    $this->assertListPermissions();
    $this->runServiceChannelMigration();

    $this->assertExpectedListItems([
      'fi' => [
        'numItems' => 6,
        'expectedTitles' => [
          'Channel 2 fi 8',
          'Channel 2 fi 7',
          'Channel 1 fi 5',
          'Channel 1 fi 4',
          'Channel 3 fi 11',
          'Channel 3 fi 10',
        ],
      ],
      'en' => [
        'numItems' => 6,
        'expectedTitles' => [
          'Channel 2 en 8',
          'Channel 2 en 7',
          'Channel 1 en 5',
          'Channel 1 en 4',
          'Channel 3 en 11',
          'Channel 3 en 10',
        ],
      ],
      'sv' => [
        'numItems' => 6,
        'expectedTitles' => [
          'Channel 2 sv 8',
          'Channel 2 sv 7',
          'Channel 1 sv 5',
          'Channel 1 sv 4',
          'Channel 3 sv 11',
          'Channel 3 sv 10',
        ],
      ],
    ]);
    // Make sure we can use actions to publish and unpublish content.
    $this->assertPublishAction();
  }

}
