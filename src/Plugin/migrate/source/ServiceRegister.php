<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\source;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\helfi_api_base\Plugin\migrate\source\HttpSourcePluginBase;

/**
 * Source plugin for retrieving data from Tpr.
 *
 * @MigrateSource(
 *   id = "tpr_service_register"
 * )
 */
class ServiceRegister extends HttpSourcePluginBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return 'TprServiceRegister';
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return ['id' => ['type' => 'string']];
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeListIterator() : \Iterator {
    $content = $this->getContent($this->configuration['url']);

    $fields = [
      'title',
      'description_short',
      'description_long',
    ];

    foreach ($content as $item) {
      $service = [];

      foreach (['fi', 'en', 'sv'] as $language) {
        $url = $this->buildCanonicalUrl(sprintf('%s?language=%s', $item['id'], $language));
        $data = $this->getContent($url);

        list('id' => $id) = $data;

        if (!isset($service[$id])) {
          $service[$id] = [];
        }

        foreach ($fields as $field) {
          $data[sprintf('%s_%s', $field, $language)] = $data[$field];
        }
        $service[$id] += $data;
      }
      yield reset($service);
    }
  }

}
