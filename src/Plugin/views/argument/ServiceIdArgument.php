<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Plugin\views\argument;

use Drupal\views\Plugin\views\argument\ArgumentPluginBase;
use Drupal\views\Plugin\views\query\Sql;

/**
 * Argument for service_id column.
 *
 * Provide the following parameters from the definition:
 * - service_id: (integer) Name of the column where weight units are stored.
 *
 * @ViewsArgument("id_or_service_id_handler")
 */
class ServiceIdArgument extends ArgumentPluginBase {

  /**
   * Build the query based upon the formula.
   */
  public function query($group_by = FALSE) : void {
    $this->ensureMyTable();

    // Separate IDs from Service IDs.
    $input = explode('|', $this->argument);
    $ids = array_filter(explode(',', $input[0]));
    $service_ids = array_filter(explode(',', $input[1]));

    if (empty($ids) && empty($service_ids)) {
      return;
    }

    $group = $this->query->setWhereGroup('OR');

    assert($this->query instanceof Sql);

    if (!empty($ids)) {
      $this->query->addWhere($group, 'id', $ids, 'IN');
    }
    if (!empty($service_ids)) {
      $this->query->addWhere($group, 'service_id', $service_ids, 'IN');
    }

  }

}
