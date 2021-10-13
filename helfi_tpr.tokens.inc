<?php

/**
 * @file
 * Contains token data for helfi_tpr.
 */

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\helfi_tpr\Entity\Unit;

/**
 * Implements hook_token_info().
 */
function helfi_tpr_token_info() {
  $info = [];

  $entity_types = [
    'tpr_unit',
    'tpr_service',
    'tpr_service_channel',
    'tpr_errand_service',
  ];

  foreach ($entity_types as $entity_type) {
    $definition = \Drupal::entityTypeManager()->getDefinition($entity_type);
    $info['types'][$entity_type] = [
      'name' => $definition->getLabel(),
      'description' => t('Custom tokens for @entity_type', [
        '@entity_type' => $definition->getLabel(),
      ]),
      'needs-data' => $entity_type,
    ];
    $info['tokens'][$entity_type]['label'] = [
      'name' => t('Label'),
    ];

    if ($entity_type == 'tpr_unit' || $entity_type == 'tpr_service') {
      $info['tokens'][$entity_type]['menu-link-parents'] = [
        'name' => t('Menu link parents'),
        'description' => t('Custom token for entity URL alias with menu parents.'),
      ];
    }
  }

  $info['tokens']['tpr_unit']['picture'] = [
    'name' => t('Picture'),
  ];
  $info['tokens']['tpr_unit']['description:value'] = [
    'name' => t('Description'),
  ];
  $info['tokens']['tpr_unit']['description:summary'] = [
    'name' => t('Description summary'),
  ];

  return $info;
}

/**
 * Implements hook_tokens().
 */
function helfi_tpr_tokens(
  $type,
  $tokens,
  array $data,
  array $options,
  BubbleableMetadata $bubbleable_metadata
) {
  $entity_types = [
    'tpr_unit',
    'tpr_service',
    'tpr_service_channel',
    'tpr_errand_service',
  ];

  if (!in_array($type, $entity_types) || empty($data[$type])) {
    return [];
  }
  /** @var \Drupal\helfi_tpr\Entity\TprEntityBase $entity */
  $entity = $data[$type];
  $replacements = [];

  foreach ($tokens as $name => $original) {
    if ($name === 'label') {
      $replacements[$original] = $entity->label();
    }

    if ($entity instanceof Unit) {
      if ($name === 'picture') {
        $replacements[$original] = $entity->getPictureUrl();
      }
      if ($name === 'description:value') {
        $replacements[$original] = $entity->getDescription('value');
      }
      if ($name === 'description:summary') {
        $replacements[$original] = $entity->getDescription('summary');
      }
    }

    // Custom token for entity URL alias with menu parents.
    if ($name == 'menu-link-parents') {
      // Get entity menu link.
      $menu_link_manager = \Drupal::service('plugin.manager.menu.link');
      $menu_link = $menu_link_manager->loadLinksByRoute('entity.' . $type . '.canonical', [$type => $entity->id()]);

      if (is_array($menu_link) && count($menu_link)) {
        $menu_link = reset($menu_link);
        if ($menu_link->getParent()) {
          // Get entity menu link parents.
          $parents = $menu_link_manager->getParentIds($menu_link->getParent());

          // Put the cleaned parent titles in an array.
          $titles = [];

          foreach (array_reverse($parents) as $parent) {
            $menu_link_item = $menu_link_manager->createInstance($parent);
            $titles[] = \Drupal::service('pathauto.alias_cleaner')->cleanString(
              token_menu_link_translated_title($menu_link_item, $options['langcode']),
              $options
            );
          }

          // Return the titles as a string separated with /'s.
          $replacements[$original] = implode('/', $titles);
        }
      }
    }
  }

  return $replacements;
}
