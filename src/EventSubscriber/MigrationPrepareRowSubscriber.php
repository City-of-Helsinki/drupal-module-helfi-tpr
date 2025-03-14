<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\EventSubscriber;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\helfi_tpr\Event\MigrationPrepareRowEvent;
use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Plugin\MigrateDestinationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber for handling row preparation in TPR migration processes.
 */
final class MigrationPrepareRowSubscriber implements EventSubscriberInterface {

  private array $allowedMigrationIds = [
    'tpr_service',
    'tpr_errand_service',
    'tpr_unit',
    'tpr_service_channel',
  ];

  /**
   * Constructs a MigrationPrepareRowSubscriber event subscriber.
   */
  public function __construct(
    private readonly Connection $connection,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Get the entity type ID from a plugin ID.
   *
   * @param string $plugin_id
   *   The plugin ID.
   *
   * @return string
   *   The entity type ID.
   */
  protected static function getEntityTypeId(MigrateDestinationInterface $plugin): string {
    $entity_type_id = NULL;
    if (strpos($plugin->getPluginId(), $plugin::DERIVATIVE_SEPARATOR)) {
      [, $entity_type_id] = explode($plugin::DERIVATIVE_SEPARATOR, $plugin->getPluginId(), 2);
    }
    return $entity_type_id;
  }

  /**
   * Check if a mapping entry exists in the for the given source.
   *
   * @param string $source_id
   *   The source ID.
   * @param \Drupal\helfi_tpr\Event\MigrationPrepareRowEvent $event
   *   The migration prepare row event.
   */
  protected function hasExistingMapping(int $source_id, MigrationPrepareRowEvent $event): bool {
    $table_name = $event->migration->getIdMap()->mapTableName();

    $query = $this->connection
      ->select($table_name, 't')
      ->condition('t.sourceid1', $source_id);

    return (bool) $query->countQuery()->execute()->fetchField();
  }

  /**
   * Check if an entity exists in the database.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $entity_id
   *   The entity ID.
   * @param string $langcode
   *   The language code.
   */
  protected function entityExists(string $entity_type_id, int $entity_id, string $langcode): bool {
    $storage = $this->entityTypeManager->getStorage($entity_type_id);

    // Do a database query since we do not need to load entity fields.
    $ids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('id', $entity_id)
      ->condition('langcode', $langcode)
      ->range(0, 1)
      ->execute();

    return !empty($ids);
  }

  /**
   * Process the prepare row event of TPR migrations.
   *
   * This ensures that any missing translations are marked for force import
   * in any cases where we have records in the mapping table but the entity
   * does not exist in the database.
   *
   * @param \Drupal\helfi_tpr\Event\MigrationPrepareRowEvent $event
   *   The migration prepare row event.
   */
  protected function processPrepareRow(MigrationPrepareRowEvent $event): void {
    $row = $event->row;

    if (!$row || $row->isStub()) {
      return;
    }

    // Get the entity type ID from the plugin.
    $plugin = $event->migration->getDestinationPlugin();
    $entity_type_id = static::getEntityTypeId($plugin);

    // Skip if we could not determine the entity type.
    if (!$entity_type_id) {
      return;
    }

    // Skip if the entity has not been migrated yet and let the migration
    // normally handle the creation of the entity.
    if (!$this->hasExistingMapping($row->getSourceProperty('id'), $event)) {
      return;
    }

    // If we have records in the mapping table but the entity does not exist,
    // mark the row for force import.
    if (!$this->entityExists($entity_type_id, $row->getSourceProperty('id'), $row->getSourceProperty('language'))) {
      $id_map = $row->getIdMap();

      // Force update or create if the destination entity does not exist.
      $id_map['source_row_status'] = MigrateIdMapInterface::STATUS_NEEDS_UPDATE;
      $row->setIdMap($id_map);
    }

  }

  /**
   * Handles the migration prepare row event.
   *
   * @param \Drupal\helfi_tpr\Event\MigrationPrepareRowEvent $event
   *   The migration prepare row event.
   */
  public function onPrepareRow(MigrationPrepareRowEvent $event): void {
    if (in_array($event->migration->id(), $this->allowedMigrationIds)) {
      $this->processPrepareRow($event);
    }
  }


  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      MigrationPrepareRowEvent::class => ['onPrepareRow'],
    ];
  }

}
