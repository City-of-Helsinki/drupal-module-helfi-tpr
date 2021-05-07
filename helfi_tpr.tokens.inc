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
  }
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

    if ($entity instanceof Unit && $name === 'picture') {
      $replacements[$original] = $entity->getPictureUrl();
    }
  }

  return $replacements;
}
