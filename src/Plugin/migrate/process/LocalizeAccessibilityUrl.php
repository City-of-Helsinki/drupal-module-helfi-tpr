<?php

namespace Drupal\helfi_tpr\Plugin\migrate\process;

use Drupal\migrate\Attribute\MigrateProcess;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\migrate\process\DefaultValue;
use Drupal\migrate\Row;

/**
 * Localize accessibility information URL.
 *
 * @code
 *  accessibility_www:
 *    plugin: localize_accessibility_www
 *    source: accessibility_www
 * @endcode
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 */
#[MigrateProcess(
  id: "localize_accessibility_www",
)]
class LocalizeAccessibilityUrl extends DefaultValue {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value)) {
      return '';
    }

    // Get the language from the row.
    $language = $row->getSource()['language'];

    // Normalize and map language to path segment.
    $segmentMap = [
      'fi' => '',
      'sv' => 'sv/',
      'en' => 'en/',
    ];
    $segment = $segmentMap[$language] ?? '';

    // Replace the /kapaesteettomyys/app/ part with the language segment value.
    return preg_replace('#/kapaesteettomyys/app/(?:en/|sv/)?#', '/kapaesteettomyys/app/' . $segment, trim($value), 1);
  }

}
