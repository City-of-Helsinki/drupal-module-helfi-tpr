<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\EventSubscriber;

use Drupal\Core\Database\Connection;
use Drupal\helfi_tpr\Event\MigrationPrepareRowEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber for handling row preparation in TPR migration processes.
 */
final class MigrationPrepareRowSubscriber implements EventSubscriberInterface {

  private array $allowedMigrationIds = [
    'tpr_service',
  ];

  /**
   * Constructs a MigrationPrepareRowSubscriber event subscriber.
   */
  public function __construct(
    private readonly Connection $connection,
  ) {}

  /**
   * Handles the migration prepare row event.
   *
   * @param \Drupal\helfi_tpr\Event\MigrationPrepareRowEvent $event
   *   The migration prepare row event.
   */
  public function onPrepareRow(MigrationPrepareRowEvent $event): void {
    if (!in_array($event->migration->id(), $this->allowedMigrationIds)) {
      return;
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
