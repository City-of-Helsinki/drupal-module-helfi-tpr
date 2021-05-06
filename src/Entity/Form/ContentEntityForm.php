<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Entity\Form;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\ContentEntityForm as CoreContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the tpr entity forms.
 *
 * @internal
 */
class ContentEntityForm extends CoreContentEntityForm {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected DateFormatterInterface $dateFormatter;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\helfi_tpr\Entity\TprEntityBase $entity */
    $entity = $this->getEntity();
    $form = parent::form($form, $form_state);

    $form['advanced']['#attributes']['class'][] = 'entity-meta';

    $form['meta'] = [
      '#type' => 'container',
      '#group' => 'advanced',
      '#weight' => -10,
      '#title' => $this->t('Status'),
      '#attributes' => ['class' => ['entity-meta__header']],
      '#tree' => TRUE,
    ];

    $form['meta']['published'] = [
      '#type' => 'item',
      '#markup' => $entity->isPublished() ? $this->t('Published') : $this->t('Not published'),
      '#wrapper_attributes' => ['class' => ['entity-meta__title']],
      '#weight' => -1,
    ];

    $form['meta']['changed'] = [
      '#type' => 'item',
      '#title' => $this->t('Last saved'),
      '#markup' => $this->dateFormatter->format($entity->getChangedTime(), 'short'),
      '#wrapper_attributes' => ['class' => ['entity-meta__last-saved']],
      '#weight' => 0,
    ];

    $form['meta']['author'] = [
      '#type' => 'item',
      '#title' => $this->t('Publisher'),
      '#markup' => $entity->getAuthor() ? $entity->getAuthor()->getAccountName() : '',
      '#wrapper_attributes' => ['class' => ['entity-meta__author']],
    ];

    $form['author_information'] = [
      '#type' => 'details',
      '#title' => $this->t('Authoring information'),
      '#group' => 'advanced',
      '#weight' => 90,
      '#optional' => TRUE,
    ];

    return $form;
  }

}
