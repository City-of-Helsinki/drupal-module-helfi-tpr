<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Entity;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a mapping for service channel types.
 *
 * @todo Convert this to enum when we can use PHP 8.1.
 */
final class ChannelType {

  public const ESERVICE = 'ESERVICE';
  public const CHAT = 'CHAT';
  public const EMAIL = 'EMAIL';
  public const TELEPHONE = 'TELEPHONE';
  public const PRINTABLE_FORM = 'PRINTABLE_FORM';
  public const LOCAL = 'LOCAL';
  public const SMS = 'SMS';
  public const WEBPAGE = 'WEBPAGE';
  public const MAIL = 'MAIL';
  public const TELEFAX = 'TELEFAX';

  /**
   * Constructs a new instance.
   *
   * @todo Convert id to readonly.
   *
   * @param string $id
   *   The type.
   * @param int $weight
   *   The weight.
   */
  public function __construct(public string $id, public int $weight) {
  }

  /**
   * Gets the label.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The label.
   */
  public function label() : TranslatableMarkup {
    return match ($this->id) {
      self::MAIL => new TranslatableMarkup('Postal mail'),
      self::EMAIL => new TranslatableMarkup('E-mail'),
      self::TELEFAX => new TranslatableMarkup('Telefax'),
      self::TELEPHONE => new TranslatableMarkup('Telephone'),
      self::SMS => new TranslatableMarkup('SMS'),
      self::LOCAL => new TranslatableMarkup('Local service'),
      self::CHAT => new TranslatableMarkup('Online chat'),
      self::ESERVICE => new TranslatableMarkup('E-service'),
      self::WEBPAGE => new TranslatableMarkup('Webpage'),
      self::PRINTABLE_FORM => new TranslatableMarkup('Printable form'),
    };
  }

}
