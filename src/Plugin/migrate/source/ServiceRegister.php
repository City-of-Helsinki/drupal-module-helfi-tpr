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
   * The total count.
   *
   * @var int
   */
  protected int $count = 0;

  /**
   * {@inheritdoc}
   */
  public function __toString() : string {
    return 'TprServiceRegister';
  }

  /**
   * {@inheritdoc}
   */
  public function count($refresh = FALSE) : int {
    if (!$this->count) {
      $this->count = count($this->getContent($this->configuration['url']));
    }
    return $this->count;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() : array {
    return ['id' => ['type' => 'string']];
  }

  /**
   * {@inheritdoc}
   */
  public function fields() : array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeListIterator() : \Iterator {
    $content = $this->getContent($this->configuration['url']);
    $processed = 0;

    foreach ($content as $item) {
      $processed++;
      // Allow number of items to be limited by using an env variable.
      if (($this->getLimit() > 0) && $processed > $this->getLimit()) {
        break;
      }

      foreach (['fi', 'en', 'sv'] as $language) {
        $url = $this->buildCanonicalUrl(sprintf('%s?language=%s', $item['id'], $language));

        if (!$data = $this->getContent($url)) {
          // If getting Finnish data was unsuccessful, do not get data for
          // other languages.
          if ($language === 'fi') {
            break;
          }
          continue;
        }

        // Always use Finnish as service's default language.
        if ($language === 'fi') {
          $data['default_langcode'] = TRUE;
        }
        $data['language'] = $language;

        yield $data;
      }
    }
  }

}
