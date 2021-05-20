<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Field\Connection;

/**
 * Provides a domain object for TPR connection data.
 */
abstract class Connection {

  /**
   * The data.
   *
   * @var array
   */
  protected array $data;

  /**
   * Checks whether given field is valid or not.
   *
   * @param string $fieldName
   *   The field name to check.
   *
   * @return bool
   *   TRUE if field is valid.
   */
  protected function isValidField(string $fieldName) : bool {
    return in_array($fieldName, $this->getFields());
  }

  /**
   * Sets the value for given field.
   *
   * @param string $field
   *   The field name.
   * @param mixed $value
   *   The value.
   *
   * @return $this
   *   The self.
   */
  public function set(string $field, $value) : self {
    if (!$this->isValidField($field)) {
      throw new \InvalidArgumentException(sprintf('Field "%s" is not valid.', $field));
    }

    if (!is_scalar($value) && !is_null($value)) {
      throw new \InvalidArgumentException(sprintf('Only scalar or null values allowed for "%s".', $field));
    }
    $this->data[$field] = $value;

    return $this;
  }

  /**
   * Gets the value.
   *
   * @param string $field
   *   The field name.
   *
   * @return mixed|null
   *   Gets the value.
   */
  public function get(string $field) {
    return $this->data[$field] ?? NULL;
  }

  /**
   * Defines allowed fields for given object.
   *
   * @return array
   *   An array of allowed field names.
   */
  abstract public function getFields() : array;

  /**
   * Builds a render array from given data.
   *
   * @return array
   *   The render array.
   */
  abstract public function build() : array;

}
