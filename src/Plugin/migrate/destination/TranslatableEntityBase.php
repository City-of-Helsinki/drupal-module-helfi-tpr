<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\destination;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\migrate\Plugin\migrate\destination\EntityContentBase;
use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a destination plugin for Tpr entities.
 */
abstract class TranslatableEntityBase extends EntityContentBase {

  /**
   * The language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected LanguageManagerInterface $languageManager;

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
    $instance = parent::create($container, $configuration, $plugin_id,
      $plugin_definition, $migration);
    $instance->languageManager = $container->get('language_manager');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function updateEntity(EntityInterface $entity, Row $row) {
    $entity = parent::updateEntity($entity, $row);
    // Always delete on rollback, even if it's "default" translation.
    $this->setRollbackAction($row->getIdMap(), MigrateIdMapInterface::ROLLBACK_DELETE);

    return $entity;
  }

}
