<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr;

/**
 * Contains helper functions for TPR Unit categories.
 */
class UnitCategoryUtility {

  public const DAYCARE = 'daycare';
  public const COMPREHENSIVE_SCHOOL = 'comprehensive school';
  public const PLAYGROUND = 'playground';

  /**
   * Map ontologyword IDs to categories.
   *
   * @var array|string[]
   */
  private static array $categoryMap = [
    603 => self::DAYCARE,
    663 => self::DAYCARE,
    601 => self::COMPREHENSIVE_SCHOOL,
    602 => self::COMPREHENSIVE_SCHOOL,
    661 => self::COMPREHENSIVE_SCHOOL,
    662 => self::COMPREHENSIVE_SCHOOL,
    475 => self::PLAYGROUND,
  ];

  /**
   * Get unit category from ontologyword ID, if defined.
   *
   * @param int $ontologyword_id
   *   The ontologyword ID.
   *
   * @return string|null
   *   The category or null if there's no match for the ID.
   */
  public static function getCategory(int $ontologyword_id) : ? string {
    if (!isset(self::$categoryMap[$ontologyword_id])) {
      return NULL;
    }
    return self::$categoryMap[$ontologyword_id];
  }

}
