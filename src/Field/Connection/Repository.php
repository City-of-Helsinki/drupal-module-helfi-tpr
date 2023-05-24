<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Field\Connection;

/**
 * A repository to map connection objects to 'section_type'.
 */
final class Repository {

  /**
   * Maps 'section_type' to corresponding class.
   *
   * @var string[]
   */
  protected array $map = [
    OpeningHour::TYPE_NAME => OpeningHour::class,
    OpeningHourObject::TYPE_NAME => OpeningHourObject::class,
    Highlight::TYPE_NAME => Highlight::class,
  ];

  /**
   * Gets the corresponding class for given section type.
   *
   * @param string $type
   *   The 'section_type' value.
   *
   * @return \Drupal\helfi_tpr\Field\Connection\Connection|null
   *   Instantiated connection class.
   */
  public function get(string $type) : ? Connection {
    if (!isset($this->map[$type])) {
      return NULL;
    }
    $class = $this->map[$type];

    if (!is_a($class, Connection::class, TRUE)) {
      throw new \LogicException(sprintf('Class "%s" does not extend %s.', $class, Connection::class));
    }

    return new $class();
  }

}
