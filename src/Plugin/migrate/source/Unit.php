<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\source;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Source plugin for retrieving unit data from tpr units.
 *
 * @MigrateSource(
 *   id = "tpr_unit",
 * )
 */
class Unit extends TprSourceBase implements ContainerFactoryPluginInterface {

  use ServiceMapTrait;

  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected EntityStorageInterface $storage;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MigrationInterface $migration = NULL
  ) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition, $migration);
    $instance->storage = $container->get('entity_type.manager')->getStorage('tpr_unit');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return 'TprUnit';
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeListIterator() : \Generator {
    $query = $this->storage->getQuery();

    $entity_ids = $query->execute();

    foreach ($entity_ids as $id) {
      $data = $this->getContent($this->buildCanonicalUrl($id));

      yield from $this->normalizeMultilingualData($data);
    }
  }

}
