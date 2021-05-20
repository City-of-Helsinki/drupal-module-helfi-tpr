<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\DataType;

use Drupal\Core\TypedData\TypedData;
use Drupal\helfi_tpr\Field\Connection\Connection;

/**
 * Provides a data type wrapping for \Drupal\helfi_tpr\Connection.
 *
 * @DataType(
 *   id = "tpr_connection_data",
 *   label = @Translation("TRP - Connection data"),
 * )
 */
class ConnectionData extends TypedData {

  /**
   * The connection object.
   *
   * @var \Drupal\helfi_tpr\Field\Connection\Connection|null
   */
  protected ?Connection $value;

  /**
   * {@inheritdoc}
   */
  public function setValue($value, $notify = TRUE) {
    if ($value && !$value instanceof Connection) {
      throw new \InvalidArgumentException(sprintf('Value assigned to "%s" is not a valid Connection object', $this->getName()));
    }
    parent::setValue($value, $notify);
  }

}
