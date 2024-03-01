<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Field;

use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Extend core's BaseFieldDefition class.
 *
 * We use this to force entity api to create dedicated SQL tables
 * for certain fields to prevent us from hitting the mysql's row limit.
 */
final class FieldDefinition extends BaseFieldDefinition {

  /**
   * {@inheritdoc}
   */
  public function isBaseField() : bool {
    return FALSE;
  }

}
