<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Field\Connection;

/**
 * Provides a domain object for TPR connection type of LINK.
 */
final class Link extends TextWithLinkBase {

  /**
   * The type name.
   *
   * @var string
   */
  public const TYPE_NAME = 'LINK';

}
