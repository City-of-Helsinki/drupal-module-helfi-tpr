<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Entity\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\DependencyInjection\AutowireTrait;
use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Utility\Error;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

/**
 * Tpr-entity delete form.
 */
final class TprDeleteForm extends ContentEntityDeleteForm {

  use AutowireTrait;

  /**
   * The constructor.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info interface.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time interface.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The time interface.
   * @param \Drupal\migrate\Plugin\MigrationPluginManagerInterface $migration_manager
   *   The migration plugin manager.
   */
  public function __construct(
    EntityRepositoryInterface $entity_repository,
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    TimeInterface $time,
    private readonly ClientInterface $http_client,
    private readonly MigrationPluginManagerInterface $migration_manager,
  ) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($this->entityExists()) {
      $this->messenger()->addWarning(
        $this->t('Cannot delete TPR-entity which still exists in the API')
      );
      return;
    }

    /** @var \Drupal\helfi_tpr\Entity\TprEntityBase $entity */
    $entity = $this->entity;
    $message = $this->getDeletionMessage();

    // Make sure that deleting a translation does not delete the whole entity.
    if (!$entity->isDefaultTranslation()) {
      $untranslated_entity = $entity->getUntranslated();
      $untranslated_entity->removeTranslation($entity->language()->getId());
      $untranslated_entity->save();
      $form_state->setRedirectUrl($untranslated_entity->toUrl('canonical'));
    }
    else {
      $entity->delete(TRUE);
      $form_state->setRedirectUrl($this->getRedirectUrl());
    }

    $this->messenger()->addStatus($message);
    $this->logDeletionMessage();
  }

  /**
   * Check if the entity being deleted exists in the API.
   *
   * @return bool
   *   The entity exists in api.
   */
  private function entityExists(): bool {
    $entityTypeId = $this->entity->getEntityTypeId();

    $urlMap = [
      'tpr_errand_service' => 'canonical_url',
      'tpr_ontology_word_details' => 'details_url',
      'tpr_service' => 'canonical_url',
      'tpr_unit' => 'url',
    ];
    $key = $urlMap[$entityTypeId];
    $url = $this->migration_manager->getDefinition($entityTypeId)['source'][$key];

    $request_url = sprintf(
      '%s/%s%s',
      rtrim(strtok($url, '?'), '/'),
      $this->entity->id(),
      "?language={$this->entity->language()->getId()}"
    );

    try {
      $response = $this->http_client
        ->request('GET', $request_url);
      $data = $response->getBody()
        ->getContents();
    }
    catch (ClientException $e) {
      if ($e->getResponse()->getStatusCode() === 404) {
        return FALSE;
      }

      Error::logException($this->getLogger('helfi_tpr'), $e);
      // Prevent from deleting.
      return TRUE;
    }

    if (!json_validate($data) || $data === 'null') {
      return FALSE;
    }

    $api_data = json_decode($data, TRUE);
    if (isset($api_data['id']) && $api_data['id'] == $this->entity->id()) {
      return TRUE;
    }

    return FALSE;
  }

}
