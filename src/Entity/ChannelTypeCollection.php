<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Entity;

/**
 * Provides a collection class for channel types.
 *
 * @todo Convert this to enum when we can use PHP 8.1.
 */
final class ChannelTypeCollection implements \ArrayAccess, \Iterator, \Countable {

  /**
   * The channel types.
   *
   * @var \Drupal\helfi_tpr\Entity\ChannelType[]
   */
  private array $channelTypes = [];

  /**
   * Constructs a new instance.
   *
   * @param \Drupal\helfi_tpr\Entity\ChannelType[] $channelTypes
   *   The channel types.
   */
  public function __construct(array $channelTypes) {
    foreach ($channelTypes as $channelType) {
      $this->channelTypes[$channelType->id] = $channelType;
    }
    $this->sort($this->channelTypes);
  }

  /**
   * Sorts channel types by weight.
   *
   * @param \Drupal\helfi_tpr\Entity\ChannelType[] $items
   *   The channel items to sort.
   */
  public function sort(array &$items) : void {
    uasort($items, function (ChannelType $a, ChannelType $b) {
      if ($a->weight === $b->weight) {
        return 0;
      }
      return $a->weight < $b->weight ? -1 : 1;
    });
  }

  /**
   * Create collection from array.
   *
   * @param array $items
   *   The items to create.
   *
   * @return $this
   *   The self.
   */
  public static function createFromArray(array $items = []) : self {
    if (!$items) {
      return self::all();
    }

    $types = [];
    foreach ($items as $key => $element) {
      $types[] = new ChannelType($key, (int) $element['weight']);
    }
    return new self($types);
  }

  /**
   * Gets all channel types.
   *
   * @return $this
   *   The self.
   */
  public static function all() : self {
    $types = [];
    $reflection = new \ReflectionClass(ChannelType::class);

    $weight = 0;
    foreach ($reflection->getConstants() as $value) {
      $types[$value] = new ChannelType($value, $weight++);
    }
    return new self($types);
  }

  /**
   * {@inheritdoc}
   */
  public function offsetExists(mixed $offset) : bool {
    return isset($this->channelTypes[$offset]);
  }

  /**
   * {@inheritdoc}
   */
  public function offsetGet(mixed $offset) : ChannelType {
    return $this->channelTypes[$offset];
  }

  /**
   * {@inheritdoc}
   */
  public function offsetSet(mixed $offset, mixed $value) : void {
    if (!$value instanceof ChannelType) {
      throw new \InvalidArgumentException('$value must be type of ChannelType.');
    }
    $this->channelTypes[$offset] = $value;
  }

  /**
   * {@inheritdoc}
   */
  public function offsetUnset(mixed $offset) : void {
    unset($this->channelTypes[$offset]);
  }

  /**
   * {@inheritdoc}
   */
  public function current() : ? ChannelType {
    if ($current = current($this->channelTypes)) {
      return $current;
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function next() : void {
    next($this->channelTypes);
  }

  /**
   * {@inheritdoc}
   */
  public function key() : ? string {
    if (!$current = $this->current()) {
      return NULL;
    }
    return $current->id;
  }

  /**
   * {@inheritdoc}
   */
  public function valid() : bool {
    if (!$current = $this->current()) {
      return FALSE;
    }
    return isset($this->channelTypes[$current->id]);
  }

  /**
   * {@inheritdoc}
   */
  public function rewind() : void {
    reset($this->channelTypes);
  }

  /**
   * {@inheritdoc}
   */
  public function count() : int {
    return count($this->channelTypes);
  }

}
