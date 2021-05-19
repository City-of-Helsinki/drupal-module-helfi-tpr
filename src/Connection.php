<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr;

use Drupal\Core\Config\Entity\ThirdPartySettingsInterface;

/**
 * Provides a domain object for TPR connection data.
 */
final class Connection implements ThirdPartySettingsInterface {

  /**
   * @inheritDoc
   */
  public function setThirdPartySetting($module, $key, $value) {
    // TODO: Implement setThirdPartySetting() method.
  }

  /**
   * @inheritDoc
   */
  public function getThirdPartySetting($module, $key, $default = NULL) {
    // TODO: Implement getThirdPartySetting() method.
  }

  /**
   * @inheritDoc
   */
  public function getThirdPartySettings($module) {
    // TODO: Implement getThirdPartySettings() method.
  }

  /**
   * @inheritDoc
   */
  public function unsetThirdPartySetting($module, $key) {
    // TODO: Implement unsetThirdPartySetting() method.
  }

  /**
   * @inheritDoc
   */
  public function getThirdPartyProviders() {
    // TODO: Implement getThirdPartyProviders() method.
  }

}
