<?php

/**
 * @file
 * Views alter for helfi_tpr.
 */

use Drupal\Core\Entity\EntityType;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ViewExecutable;

/**
 * Implements hook_views_query_alter().
 */
function helfi_tpr_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {
  if ($view->id() == 'unit_search' || $view->id() == 'service_units') {
    assert(isset($query->orderby));
    // Use the CASE function from helfi_tpr_views_pre_execute() for sorting.
    $query->orderby[0]['field'] = 'name_sort';
    $query->orderby[0]['direction'] = 'ASC';
  }
}

/**
 * Implements hook_views_pre_execute().
 */
function helfi_tpr_views_pre_execute(ViewExecutable $view) {
  if ($view->id() == 'unit_search' || $view->id() == 'service_units') {
    // CASE function for using the name_override field as default for sorting.
    // If that field is empty, use the name field.
    $view->build_info['query']->addExpression('
      CASE
        WHEN tpr_unit_field_data.name_override IS NULL THEN tpr_unit_field_data.name
        ELSE tpr_unit_field_data.name_override
      END COLLATE utf8mb4_swedish_ci', 'name_sort'
    );
  }
}

/**
 * Implements hook_views_query_substitutions().
 */
function helfi_tpr_views_query_substitutions(ViewExecutable $view) {

  // Get permission name dynamically.
  $base_entity = $view->getBaseEntityType();
  if ($base_entity instanceof EntityType) {
    $entity_permission = 'view unpublished ' . $base_entity->id();
  }
  // Default to no access if entity type can't be loaded.
  else {
    return ['***VIEW_UNPUBLISHED_TPR_ENTITIES***' => 0];
  }

  $account = \Drupal::currentUser();
  return [
    '***VIEW_UNPUBLISHED_TPR_ENTITIES***' => intval($account->hasPermission($entity_permission)),
  ];
}
