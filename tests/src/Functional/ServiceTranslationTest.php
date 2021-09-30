<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Core\Url;
use Drupal\helfi_tpr\Entity\Service;
use Drupal\Tests\helfi_tpr\Traits\TprMigrateTrait;

/**
 * Tests Service translations.
 *
 * @group helfi_tpr
 */
class ServiceTranslationTest extends MigrationTestBase {

  use TprMigrateTrait;

  /**
   * Make sure we can publish and unpublish single translations.
   */
  public function testPublish() : void {
    // Create service entity with no translations.
    $service = Service::create([
      'id' => 1,
      'name' => 'Name fi',
      'langcode' => 'fi',
      'content_translation_status' => FALSE,
    ]);
    $service->save();

    $this->drupalLogin($this->privilegedAccount);

    // Make sure we can publish and unpublish entities with no translations.
    $this->drupalGet(Url::fromRoute('entity.tpr_service.edit_form', ['tpr_service' => 1]), [
      'query' => ['language' => 'fi'],
    ]);
    $this->assertSession()->checkboxNotChecked('content_translation[status]');

    foreach ([TRUE, FALSE] as $expected_status) {
      $this->submitForm([
        'content_translation[status]' => $expected_status,
      ], 'Save');
      $this->drupalGet(Url::fromRoute('entity.tpr_service.edit_form', ['tpr_service' => 1]), [
        'query' => ['language' => 'fi'],
      ]);
      $this->assertSession()->fieldValueEquals('content_translation[status]', $expected_status);
    }

    // Run migrate to update existing service entity and translations.
    $this->runServiceMigrate();

    // Make sure all translations can be unpublished.
    foreach (['en', 'sv', 'fi'] as $language) {
      $this->drupalGet(Url::fromRoute('entity.tpr_service.edit_form', ['tpr_service' => 1]), [
        'query' => ['language' => $language],
      ]);
      $this->assertSession()->checkboxNotChecked('content_translation[status]');

      foreach ([TRUE, FALSE] as $expected_status) {
        $this->submitForm([
          'content_translation[status]' => $expected_status,
        ], 'Save');
        $this->drupalGet(Url::fromRoute('entity.tpr_service.edit_form', ['tpr_service' => 1]), [
          'query' => ['language' => $language],
        ]);
        $this->assertSession()->fieldValueEquals('content_translation[status]', $expected_status);
      }
    }
  }

}
