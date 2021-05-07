<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Core\Url;
use Drupal\Tests\helfi_api_base\Functional\MigrationTestBase;
use Drupal\Tests\helfi_tpr\Traits\UnitMigrateTrait;

/**
 * Tests Unit translations.
 *
 * @group helfi_tpr
 */
class UnitTranslationTest extends MigrationTestBase {

  use UnitMigrateTrait;

  /**
   * Make sure we can publish and unpublish single translations.
   */
  public function testPublish() : void {
    // Create unit entity with no translations.
    $unit = [
      'id' => 999,
      'name_fi' => 'Name fi',
      'modified_time' => '2015-05-16T20:01:01',
    ];
    $this->runMigrate([$unit]);

    $this->drupalLogin($this->privilegedAccount);

    // Make sure we can publish and unpublish entities with no translations.
    $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 999]), [
      'query' => ['language' => 'fi'],
    ]);
    $this->assertSession()->checkboxNotChecked('content_translation[status]');

    foreach ([TRUE, FALSE] as $expected_status) {
      $this->submitForm([
        'content_translation[status]' => $expected_status,
      ], 'Save');
      $this->assertSession()->fieldValueEquals('content_translation[status]', $expected_status);
    }

    // Create translations and make sure we can (un)/publish them all.
    $unit += [
      'name_sv' => 'Name sv',
      'name_en' => 'Name en',
    ];
    $this->runMigrate([$unit]);

    foreach (['en', 'sv', 'fi'] as $language) {
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 999]), [
        'query' => ['language' => $language],
      ]);
      $this->assertSession()->checkboxNotChecked('content_translation[status]');

      foreach ([TRUE, FALSE] as $expected_status) {
        $this->submitForm([
          'content_translation[status]' => $expected_status,
        ], 'Save');
        $this->assertSession()->fieldValueEquals('content_translation[status]', $expected_status);
      }
    }
  }

}
