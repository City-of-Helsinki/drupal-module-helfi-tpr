<?php

/**
 * @file
 * Views alter for helfi_tpr.
 */

use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ViewExecutable;

/**
 * Implements hook_views_query_alter().
 */
function helfi_tpr_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {
  if ($view->id() == 'unit_search') {
    // Use the CASE function from helfi_tpr_views_pre_execute() for sorting.
    $query->orderby[0]['field'] = 'name_sort';
    $query->orderby[0]['direction'] = 'ASC';
  }
}

/**
 * Implements hook_views_pre_execute().
 */
function helfi_tpr_views_pre_execute(ViewExecutable $view) {
  if ($view->id() == 'unit_search') {
    // CASE function for using the name_override field as default for sorting.
    // If that field is empty, use the name field.
    $view->build_info['query']->addExpression('CASE WHEN tpr_unit_field_data.name_override IS NULL THEN tpr_unit_field_data.name ELSE tpr_unit_field_data.name_override END', 'name_sort');
  }
}
