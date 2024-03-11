<?php

namespace Drupal\helfi_tpr\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\helfi_api_base\Event\TokenEvent;
use Drupal\helfi_tpr\Entity\Service;
use Drupal\helfi_tpr\Entity\Unit;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Implements shareable image token for tpr units.
 */
final class TokenSubscriber implements EventSubscriberInterface {

  /**
   * Constructs a new instance.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
  }

  /**
   * React to token event.
   *
   * @param \Drupal\helfi_api_base\Event\TokenEvent $event
   *   Token event.
   */
  public function handleTokens(TokenEvent $event) : void {
    $entity_types = [
      'tpr_unit',
      'tpr_service',
      'tpr_service_channel',
      'tpr_errand_service',
    ];

    if (!in_array($event->type, $entity_types) || empty($event->data[$event->type])) {
      return;
    }

    /** @var \Drupal\helfi_tpr\Entity\TprEntityBase $entity */
    $entity = $event->data[$event->type];

    switch ($event->token) {
      case 'label':
        $event->setReplacementValue($entity->label());
        break;

      case 'description:value':
        if ($entity instanceof Unit || $entity instanceof Service) {
          $event->setReplacementValue($entity->getDescription('value'));
        }
        break;

      case 'description:summary':
        if ($entity instanceof Unit || $entity instanceof Service) {
          $event->setReplacementValue($entity->getDescription('summary'));
        }
        break;

      // Picture token is replaced for backwards compatability. The
      // shareable-image token is handled by other event subscribers
      // in helfi platform and may already have default image that this
      // handler overrides.
      case 'picture':
      case 'shareable-image':
        if ($entity instanceof Unit) {
          /** @var \Drupal\image\ImageStyleInterface $image_style */
          $image_style = $this->entityTypeManager
            ->getStorage('image_style')
            ->load('og_image');

          $event->setReplacementValue($entity->getPictureUrl($image_style));
        }
        break;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      TokenEvent::class => ['handleTokens'],
    ];
  }

}
