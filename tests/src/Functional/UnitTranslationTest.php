<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Core\Url;
use Drupal\helfi_tpr\Entity\Unit;
use Drupal\Tests\helfi_tpr\Traits\TprMigrateTrait;

/**
 * Tests Unit translations.
 *
 * @group helfi_tpr
 */
class UnitTranslationTest extends MigrationTestBase {

  use TprMigrateTrait;

  /**
   * Make sure we can publish and unpublish single translations.
   */
  public function testPublish() : void {
    // Create unit entity with no translations.
    $unit = Unit::create([
      'id' => 1,
      'name' => 'Name fi',
      'langcode' => 'fi',
      'content_translation_status' => FALSE,
    ]);
    $unit->save();

    $this->drupalLogin($this->privilegedAccount);

    // Make sure we can publish and unpublish entities with no translations.
    $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 1]), [
      'query' => ['language' => 'fi'],
    ]);
    $this->assertSession()->checkboxNotChecked('content_translation[status]');

    foreach ([TRUE, FALSE] as $expected_status) {
      $this->submitForm([
        'content_translation[status]' => $expected_status,
      ], 'Save');
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 1]), [
        'query' => ['language' => 'fi'],
      ]);
      $this->assertSession()->fieldValueEquals('content_translation[status]', (string) $expected_status);
    }

    // Run migrate to update existing unit entity andtranslations.
    $this->runUnitMigrate();

    // Make sure all translations can be unpublished.
    foreach (['en', 'sv', 'fi'] as $language) {
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 1]), [
        'query' => ['language' => $language],
      ]);
      $this->assertSession()->checkboxNotChecked('content_translation[status]');

      foreach ([TRUE, FALSE] as $expected_status) {
        $this->submitForm([
          'content_translation[status]' => $expected_status,
        ], 'Save');
        $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 1]), [
          'query' => ['language' => $language],
        ]);
        $this->assertSession()->fieldValueEquals('content_translation[status]', (string) $expected_status);
      }
    }
  }

}
