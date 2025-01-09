<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Entity\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Utility\Error;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Tpr-entity delete form.
 */
class TprDeleteForm extends ContentEntityDeleteForm {

  /**
   * The constructor.
   *
   * @param EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info interface.
   * @param TimeInterface $time
   *   The time interface.
   * @param ClientInterface $http_client
   *   The time interface.
   */
  public function __construct(
    EntityRepositoryInterface $entity_repository,
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    TimeInterface $time,
    private readonly ClientInterface $http_client,
  ) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
  }

  /**
   * {inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('http_client'),
    );
  }

  /**
   * {inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($this->helfiTprEntityExists()) {
      $this->messenger()->addWarning(
        $this->t('Cannot delete TPR-entity which still exists in the API')
      );
      return;
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * Check if the entity being deleted exists in the API.
   *
   * @return bool
   *   The entity exists in api.
   */
  private function helfiTprEntityExists(): bool {
    $entityTypeId = $this->entity->getEntityTypeId();
    /** @var \Drupal\migrate\Plugin\MigrationPluginManager $migrationPluginManager */
    $migrationPluginManager = \Drupal::service('plugin.manager.migration');

    $urlMap = [
      'tpr_errand_service' => 'canonical_url',
      'tpr_ontology_word_details' => 'details_url',
      'tpr_service' => 'canonical_url',
      'tpr_unit' => 'url',
    ];
    $key = $urlMap[$entityTypeId];
    $url = $migrationPluginManager->getDefinition($entityTypeId)['source'][$key];

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
