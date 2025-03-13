<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\MigrateSourceInterface;

class MigrationPrepareRowEvent extends Event {

  /**
   * Constructs a MigrationPrepareRowEvent object.
   *
   * @param \Drupal\migrate\Row $row
   *   The row that is being prepared.
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   The migration plugin.
   * @param \Drupal\migrate\Plugin\MigrateSourceInterface $source
   *   The migration source.
   */
  public function __construct(
    public readonly Row $row,
    public readonly MigrationInterface $migration,
    public readonly MigrateSourceInterface $source,
  ) {}

}
