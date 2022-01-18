<?php

declare(strict_types = 1);

namespace Drupal\helfi_school_addons;

/**
 * Contains helper functions for school addons.
 */
class SchoolUtility {

  /**
   * School year key used with State API.
   */
  private const SCHOOL_YEAR_KEY = 'helfi_school_addons.school_year';

  /**
   * Helper function to get the current school year.
   *
   * @return string|null
   *   The current school year, e.g. "2022-2023".
   */
  public static function getCurrentSchoolYear(): ?string {
    return \Drupal::state()->get(self::SCHOOL_YEAR_KEY);
  }

  /**
   * Helper function to set the current school year.
   *
   * @param string $school_year
   *   The current school year, e.g. "2022-2023".
   */
  public static function setCurrentSchoolYear(string $school_year) {
    \Drupal::state()->set(self::SCHOOL_YEAR_KEY, $school_year);
  }

}
