<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\source;

/**
 * A helper trait to interact with service map API.
 */
trait ServiceMapTrait {

  /**
   * Converts one multilingual object into multiple objects.
   *
   * By default a TPR entity has langcode suffixed fields for multilingual
   * data, like:
   * @code
   * name_fi => Name in finnish
   * name_sv => Name in swedish
   * www_fi => http://finnish-link
   * www_sv => http://swedish-link
   * @endcode
   *
   * Our destination plugin expects translations to be returned as
   * separate objects and have identical field names.
   *
   * @param array $data
   *   The data from API.
   *
   * @return \Generator
   *   The iterator.
   */
  protected function normalizeMultilingualData(array $data) : \Generator {
    $delta = 0;

    foreach (['fi', 'sv', 'en'] as $language) {
      // Skip translations without translated names.
      if (!isset($data[sprintf('name_%s', $language)])) {
        continue;
      }
      $item = $data;

      // Mark first item as default langcode.
      if ($delta === 0) {
        $item['default_langcode'] = TRUE;
      }
      $delta++;

      $item['language'] = $language;

      // 'Normalize' suffixed fields to have a same name for every language.
      // For example: name_fi, name_sv => name.
      foreach ($this->configuration['translatable_fields'] ?? [] as $field) {
        $key = sprintf('%s_%s', $field, $language);

        if (!isset($data[$key])) {
          continue;
        }
        $item[$field] = $data[$key];
      }

      // Normalize accessibility sentences separately since they are
      // grouped inside a nested array.
      if (isset($item['accessibility_sentences'])) {
        foreach ($item['accessibility_sentences'] as $sentence) {
          [$sentence_group_key, $sentence_key] = [
            "sentence_group_$language",
            "sentence_$language",
          ];
          // Skip untranslated sentencens.
          if (!isset($sentence[$sentence_group_key], $sentence[$sentence_key])) {
            continue;
          }
          $item['accessibility_sentences'][] = [
            'sentence_group' => $sentence[$sentence_group_key],
            'sentence' => $sentence[$sentence_key],
          ];
        }
      }
      yield $item;
    }
  }

}
