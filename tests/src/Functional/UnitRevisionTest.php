<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Core\Url;
use Drupal\helfi_tpr\Entity\Unit;
use Drupal\Tests\helfi_tpr\Traits\TprMigrateTrait;

/**
 * Tests unit revisions.
 *
 * @group helfi_tpr
 */
class UnitRevisionTest extends MigrationTestBase {

  use TprMigrateTrait;

  /**
   * Tests revisions.
   */
  public function testRevision() : void {
    $this->drupalLogin($this->privilegedAccount);
    $this->runUnitMigrate();

    foreach (['fi', 'sv'] as $language) {
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 1]), [
        'query' => ['language' => $language],
      ]);
      $this->assertSession()->statusCodeEquals(200);
      // Make sure translation is not published by default.
      $this->assertSession()->checkboxNotChecked('content_translation[status]');

      $this->submitForm([
        'content_translation[status]' => TRUE,
        'revision' => TRUE,
        'revision_log[0][value]' => "Revision log $language",
        'name_override[0][value]' => "Override name $language",
      ], 'Save');

      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 1]), [
        'query' => ['language' => $language],
      ]);
      $this->assertSession()->fieldValueEquals('name_override[0][value]', "Override name $language");
      // Make sure TPR data is unchanged when the form is saved.
      $this->assertSession()->pageTextContains("pvm $language 1");
      $this->assertSession()->pageTextContains("Name $language 1");

      // Go to revisions tab and make sure it's visible.
      $this->getSession()->getPage()->findLink('Revisions')->click();

      $expected_url = Url::fromRoute('entity.tpr_unit.version_history', ['tpr_unit' => 1])->toString();
      $this->assertSession()->addressEquals($expected_url);
      $this->assertSession()->statusCodeEquals(200);

      // Make sure we have more than one revision.
      $count = $this->getSession()->getPage()->findAll('css', 'table > tbody > tr');
      $this->assertTrue(count($count) >= 2);

      // Revert back to first revision.
      $this->getSession()->getPage()->find('css', 'table tr:last-of-type li a')->click();
      $this->getSession()->getPage()->pressButton('Revert');
      // Make sure we have status message saying that revision was reverted.
      $this->assertSession()->pageTextContains('has been reverted to the revision from');

      // Make sure we have more revisions than before reverting.
      $count = $this->getSession()->getPage()->findAll('css', 'table > tbody > tr');
      $this->assertTrue(count($count) >= 3);
    }

    // Update TPR data and make sure reverting revisions doesn't revert TPR
    // fields.
    Unit::load(1)
      ->getTranslation('fi')
      ->set('name', 'Name updated fi')
      ->set('call_charge_info', 'pvm fi updated 1')
      ->getTranslation('sv')
      ->set('name', 'Name updated sv')
      ->set('call_charge_info', 'pvm sv updated 1')
      ->save();

    foreach (['fi', 'sv'] as $language) {
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 1]), [
        'query' => ['language' => $language],
      ]);
      // Make sure TPR data is updated.
      $this->assertSession()->pageTextContains("pvm $language updated 1");
      $this->assertSession()->pageTextContains("Name updated $language");

      // Revert back to first revision.
      $this->getSession()->getPage()->findLink('Revisions')->click();
      $this->getSession()->getPage()->find('css', 'table tr:last-of-type li a')->click();
      $this->getSession()->getPage()->pressButton('Revert');

      // Make sure TPR data is unchanged.
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 1]), [
        'query' => ['language' => $language],
      ]);
      $this->assertSession()->pageTextContains("pvm $language updated 1");
      $this->assertSession()->pageTextContains("Name updated $language");
      // Our override field should be empty because we reverted back to the
      // original version.
      $this->assertSession()->fieldValueEquals('name_override[0][value]', '');
    }
  }

}
