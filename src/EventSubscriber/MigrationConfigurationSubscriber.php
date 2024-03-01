<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\helfi_api_base\Event\MigrationConfigurationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * An event subscriber for migration configuration changes.
 */
final class MigrationConfigurationSubscriber implements EventSubscriberInterface {

  /**
   * Constructs a new instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration factory.
   */
  public function __construct(private ConfigFactoryInterface $configFactory) {
  }

  /**
   * Responds to migration configuration event.
   *
   * @param \Drupal\helfi_api_base\Event\MigrationConfigurationEvent $event
   *   The event to responds to.
   */
  public function onMigration(MigrationConfigurationEvent $event) {
    if (!str_starts_with($event->migration->id(), 'tpr_')) {
      return;
    }
    $config = $this->configFactory
      ->get('helfi_tpr.migration_settings.' . $event->migration->id());

    if (!$config) {
      return;
    }

    if ($url = $config->get('url')) {
      $event->configuration['url'] = $url;
    }

    if ($canonicalUrl = $config->get('canonical_url')) {
      $event->configuration['canonical_url'] = $canonicalUrl;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() : array {
    return [
      'Drupal\helfi_api_base\Event\MigrationConfigurationEvent' => ['onMigration'],
    ];
  }

}
