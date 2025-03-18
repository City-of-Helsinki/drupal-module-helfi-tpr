<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\Select;
use Drupal\Core\Database\StatementInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\helfi_tpr\Event\MigrationPrepareRowEvent;
use Drupal\helfi_tpr\EventSubscriber\MigrationPrepareRowSubscriber;
use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Row;
use Prophecy\Argument;

/**
 * Tests the TPR migration prepare row subscriber.
 *
 * @group helfi_tpr
 */
class MigrationPrepareRowSubscriberTest extends MigrationTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['remote_entity_test'];

  /**
   * Creates a mock database connection that expects a mapping to exist.
   */
  protected function mockDatabaseConnection(bool $mappingExists = TRUE): Connection {
    $statement = $this->prophesize(StatementInterface::class);
    $statement->fetchField()->willReturn($mappingExists ? 1 : 0);

    $select = $this->prophesize(Select::class);
    $select->condition(Argument::any(), Argument::any())->willReturn($select);
    $select->countQuery()->willReturn($select);
    $select->execute()->willReturn($statement->reveal());

    $connection = $this->prophesize(Connection::class);
    $connection->select(Argument::any(), Argument::any())
      ->willReturn($select->reveal());

    return $connection->reveal();
  }

  /**
   * Creates a mock entity storage that can simulate translation existence.
   */
  protected function mockEntityStorage(bool $translationExists = FALSE): EntityStorageInterface {
    $query = $this->prophesize(QueryInterface::class);
    $query->accessCheck(Argument::any())->willReturn($query);
    $query->condition(Argument::any(), Argument::any())->willReturn($query);
    $query->range(Argument::any(), Argument::any())->willReturn($query);
    $query->execute()->willReturn($translationExists ? ['1' => '1'] : []);

    $storage = $this->prophesize(EntityStorageInterface::class);
    $storage->getQuery()->willReturn($query->reveal());

    return $storage->reveal();
  }

  /**
   * Creates a mock entity type manager.
   */
  protected function mockEntityTypeManager(EntityStorageInterface $storage): EntityTypeManagerInterface {
    $entityTypeManager = $this->prophesize(EntityTypeManagerInterface::class);
    $entityTypeManager->getStorage(Argument::any())->willReturn($storage);

    return $entityTypeManager->reveal();
  }

  /**
   * Tests that the missing entity translation is marked for migration import.
   */
  public function testMissingEntityTranslation(): void {
    // Initialise the row with a status of imported.
    $row = new Row(['id' => 1, 'language' => 'en'], ['id' => []]);
    $row->setIdMap(['source_row_status' => MigrateIdMapInterface::STATUS_IMPORTED]);

    $migration = $this->getMigration('tpr_service');
    $source = $migration->getSourcePlugin();

    // Mock the database with the state that the mapping exists.
    $connection = $this->mockDatabaseConnection(TRUE);

    // Mock the entity storage indicating that the entity
    // translation does not exist.
    $entityTypeManager = $this->mockEntityTypeManager($this->mockEntityStorage(FALSE));

    $event = new MigrationPrepareRowEvent($row, $migration, $source);

    $sut = new MigrationPrepareRowSubscriber($connection, $entityTypeManager);
    $sut->onPrepareRow($event);

    // Assert that the row status is set to needs update.
    $this->assertEquals(
      MigrateIdMapInterface::STATUS_NEEDS_UPDATE,
      $row->getIdMap()['source_row_status']
    );
  }

  /**
   * Test that the row status is not updated if the entity translation exists.
   */
  public function testExistingEntityTranslation(): void {
    // Initialise the row with a status of imported.
    $row = new Row(['id' => 1, 'language' => 'en'], ['id' => []]);
    $row->setIdMap(['source_row_status' => MigrateIdMapInterface::STATUS_IMPORTED]);

    $migration = $this->getMigration('tpr_service');
    $source = $migration->getSourcePlugin();

    // Mock the database with the state that the mapping exists.
    $connection = $this->mockDatabaseConnection(TRUE);

    // Mock the entity storage to indicate that the entity translation exists.
    $entityTypeManager = $this->mockEntityTypeManager($this->mockEntityStorage(TRUE));

    $event = new MigrationPrepareRowEvent($row, $migration, $source);

    $sut = new MigrationPrepareRowSubscriber($connection, $entityTypeManager);
    $sut->onPrepareRow($event);

    // Assert that the row status is not updated.
    $this->assertEquals(
      MigrateIdMapInterface::STATUS_IMPORTED,
      $row->getIdMap()['source_row_status']
    );
  }

}
