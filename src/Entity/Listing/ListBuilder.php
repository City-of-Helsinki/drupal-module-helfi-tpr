<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Entity\Listing;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a list controller for the tpr entity types.
 */
class ListBuilder extends EntityListBuilder {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected DateFormatterInterface $dateFormatter;

  /**
   * Constructs a new object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Routing\RedirectDestinationInterface $redirect_destination
   *   The redirect destination service.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatterInterface $date_formatter, RedirectDestinationInterface $redirect_destination) {
    parent::__construct($entity_type, $storage);
    $this->dateFormatter = $date_formatter;
    $this->redirectDestination = $redirect_destination;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter'),
      $container->get('redirect.destination')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['table'] = parent::render();

    $total = $this->getStorage()
      ->getQuery()
      ->count()
      ->accessCheck(FALSE)
      ->execute();

    $build['summary']['#markup'] = $this->t('Total @total', ['@total' => $total]);
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['created'] = $this->t('Created');
    $header['changed'] = $this->t('Updated');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\helfi_tpr\Entity\TprEntityBase $entity */
    $row['id'] = $entity->toLink($entity->id());
    $row['created'] = $this->dateFormatter->format($entity->getCreatedTime());
    $row['changed'] = $this->dateFormatter->format($entity->getChangedTime());
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    $destination = $this->redirectDestination->getAsArray();
    foreach ($operations as $key => $operation) {
      $operations[$key]['query'] = $destination;
    }
    return $operations;
  }

}
