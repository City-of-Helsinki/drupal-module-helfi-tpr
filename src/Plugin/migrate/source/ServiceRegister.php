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
    return 'TprServiceregister';
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

    foreach ($content as $object) {
      $url = $this->buildCanonicalUrl((string) $object['id']);

      $data = [
        'fi' => $this->getContent($url),
      ];
      // Read language specific data.
      foreach ($data['provided_languages'] as $language) {
        $data[$language] = $this->getContent(sprintf('%s?language=%s', $url, $language));
      }
      yield $data;
    }
  }

}
