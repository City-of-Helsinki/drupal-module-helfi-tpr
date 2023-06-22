<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Field\Connection;

/**
 * Provides a DTO for TPR connection type of OPENING_HOURS.
 */
final class OpeningHour extends TextWithLinkBase {

  /**
   * The type name.
   *
   * @var string
   */
  public const TYPE_NAME = 'OPENING_HOURS';

}
