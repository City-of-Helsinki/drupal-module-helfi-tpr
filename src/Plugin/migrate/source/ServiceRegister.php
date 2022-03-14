<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\source;

/**
 * Source plugin for retrieving data from Tpr.
 *
 * @MigrateSource(
 *   id = "tpr_service_register"
 * )
 */
class ServiceRegister extends TprSourceBase {

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
  protected function initializeSingleImportIterator(): \Iterator {
    foreach ($this->entityIds as $entityId) {
      $content = [];
      // We don't know which translation we're trying to update so make sure
      // to update every translation.
      foreach (['fi', 'en', 'sv'] as $language) {
        $url = $this->buildCanonicalUrl((string) $entityId) . '?language=' . $language;

        if (!$data = $this->getContent($url)) {
          continue;
        }
        $content[$language] = $data;
      }
      yield from $this->normalizeMultilingualData($content);
    }
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
      yield $content[$language] + [
        'language' => $language,
      ];
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
