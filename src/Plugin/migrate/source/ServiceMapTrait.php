<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\source;

use Drupal\helfi_tpr\Field\Connection\Repository;

/**
 * A helper trait to interact with service map API.
 */
trait ServiceMapTrait {

  /**
   * Converts one multilingual object into multiple objects.
   *
   * By default, a TPR entity has langcode suffixed fields for multilingual
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
    foreach (['fi', 'sv', 'en'] as $language) {
      // Skip translations without translated names.
      if (!isset($data[sprintf('name_%s', $language)])) {
        continue;
      }
      $item = $data;

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

      if (isset($item['connections'])) {
        $item['connections'] = $this->normalizeConnections($item['connections'], $language);
      }

      // Normalize accessibility sentences separately since they are
      // grouped inside a nested array.
      if (isset($item['accessibility_sentences'])) {
        $item['accessibility_sentences'] = $this->normalizeAccessibilitySentences($item['accessibility_sentences'], $language);
      }

      // Add scheme to www URL if missing from TPR data.
      if (isset($item['www'])) {
        $scheme = parse_url($item['www'], PHP_URL_SCHEME);
        if (empty($scheme)) {
          $item['www'] = 'https://' . $item['www'];
        }
      }

      yield $item;
    }
  }

  /**
   * Normalizes connections.
   *
   * @param array $connections
   *   The connections.
   * @param string $language
   *   The current language.
   *
   * @return array
   *   An array of connections.
   */
  protected function normalizeConnections(array $connections, string $language) : array {
    $repository = new Repository();

    $items = [];
    foreach ($connections as $connection) {
      // Skip unsupported 'section_types'.
      if (!$object = $repository->get($connection['section_type'])) {
        continue;
      }

      foreach ($object->getFields() as $field) {
        $localized_field = sprintf('%s_%s', $field, $language);

        // Connections have two different kind of fields, fields that
        // are translated and suffixed with langcode, like name_fi and
        // www_fi and non-translated fields, like contact_person or email.
        if (isset($connection[$field])) {
          $object->set($field, $connection[$field]);
        }
        if (isset($connection[$localized_field])) {
          $object->set($field, $connection[$localized_field]);
        }
      }
      // Skip untranslated items.
      if (!$name = $object->get('name')) {
        continue;
      }
      $items[] = [
        'value' => $name,
        'type' => $connection['section_type'],
        'data' => $object,
      ];
    }

    return $items;
  }

  /**
   * Normalizes accessibility sentences.
   *
   * @param array $sentences
   *   The accessibility sentences.
   * @param string $language
   *   The current language.
   *
   * @return array
   *   An array of accessibility sentences.
   */
  protected function normalizeAccessibilitySentences(array $sentences, string $language) : array {
    $items = [];
    foreach ($sentences as $sentence) {
      [$sentence_group_key, $sentence_key] = [
        "sentence_group_$language",
        "sentence_$language",
      ];
      // Skip untranslated sentences.
      if (!isset($sentence[$sentence_group_key], $sentence[$sentence_key])) {
        continue;
      }
      $items[] = [
        'sentence_group' => $sentence[$sentence_group_key],
        'sentence' => $sentence[$sentence_key],
      ];
    }

    return $items;
  }

}
