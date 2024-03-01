<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Plugin\migrate\source;

/**
 * Source plugin for retrieving service channel data from errand services.
 *
 * @MigrateSource(
 *   id = "tpr_service_channel",
 * )
 */
class ServiceChannel extends TprSourceBase {

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
    return 'TprServiceChannel';
  }

  /**
   * {@inheritdoc}
   */
  public function count($refresh = FALSE) : int {
    return $this->count;
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeSingleImportIterator(): \Iterator {
    throw new \LogicException('Not supported.');
  }

  /**
   * Converts one multilingual object into multiple objects.
   *
   * @param array $content
   *   The data from API.
   *
   * @return \Generator
   *   The iterator.
   */
  protected function normalizeMultilingualData(array $content) : \Generator {
    foreach (['fi', 'en', 'sv'] as $language) {
      if (empty($content[$language])) {
        if ($language === 'fi') {
          // If getting Finnish data was unsuccessful, do not get data for
          // other languages.
          break;
        }
        continue;
      }

      foreach ($content[$language]['channels'] as $channel) {
        yield $channel + [
          'language' => $language,
        ];
      }
    }
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
      yield from $this->normalizeMultilingualData($item);
    }
  }

}
