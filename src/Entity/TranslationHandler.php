<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Entity;

use Drupal\content_translation\ContentTranslationHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Translation handler for TPR entities.
 */
final class TranslationHandler extends ContentTranslationHandler {

  /**
   * {@inheritdoc}
   */
  public function entityFormAlter(array &$form, FormStateInterface $form_state, EntityInterface $entity) {
    parent::entityFormAlter($form, $form_state, $entity);

    $metadata = $this->manager->getTranslationMetadata($entity);

    $form['content_translation'] = [
      '#type' => 'details',
      '#title' => $this->t('Translation'),
      '#tree' => TRUE,
      '#weight' => 10,
      '#access' => $this->getTranslationAccess($entity, 'update')->isAllowed(),
      '#multilingual' => TRUE,
      '#group' => 'advanced',
      '#attributes' => [
        'class' => ['entity-translation-options'],
      ],
    ];

    $form['content_translation']['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('This translation is published'),
      '#default_value' => $metadata->isPublished(),
    ];

    $form['content_translation']['uid'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Authored by'),
      '#target_type' => 'user',
      '#default_value' => $metadata->getAuthor(),
      // Validation is done by static::entityFormValidate().
      '#validate_reference' => FALSE,
      '#maxlength' => 60,
      '#description' => $this->t('Leave blank for %anonymous.', [
        '%anonymous' => \Drupal::config('user.settings')->get('anonymous'),
      ]),
    ];

    $form['#process'][] = [$this, 'entityFormSharedElements'];

    // Process the submitted values before they are stored.
    $form['#entity_builders'][] = [$this, 'entityFormEntityBuild'];

    // Handle entity validation.
    $form['#validate'][] = [$this, 'entityFormValidate'];

    // Disable delete translation button.
    unset($form['actions']['delete_translation']);
  }

  /**
   * {@inheritdoc}
   */
  protected function addTranslatabilityClue(&$element) {
    // Do nothing since we don't care if elements are translated
    // per language or not.
  }

}
