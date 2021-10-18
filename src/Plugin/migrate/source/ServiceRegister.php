<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\source;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Source plugin for retrieving data from Tpr.
 *
 * @MigrateSource(
 *   id = "tpr_service_register"
 * )
 */
class ServiceRegister extends TprSourceBase implements ContainerFactoryPluginInterface {

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
  protected function getCanonicalBaseUrl() : string {
    if (!isset($this->configuration['canonical_url'])) {
      throw new \InvalidArgumentException('The "canonical_url" configuration is missing.');
    }
    return $this->configuration['canonical_url'];
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
        if (!$data = $item[$language]) {
          if ($language === 'fi') {
            // If getting Finnish data was unsuccessful, do not get data for
            // other languages.
            break;
          }
          continue;
        }

        if ($language === 'fi') {
          // Always use Finnish as service's default language.
          $data['default_langcode'] = TRUE;
        }
        $data['language'] = $language;

        yield $data;
      }
    }
  }

}
