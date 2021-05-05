<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Core\Url;
use Drupal\Tests\helfi_api_base\Functional\MigrationTestBase;
use Drupal\Tests\helfi_api_base\Traits\ApiTestTrait;
use Drupal\user\UserInterface;
use GuzzleHttp\Psr7\Response;

/**
 * Tests unit revisions.
 *
 * @group helfi_tpr
 */
class UnitRevisionTest extends MigrationTestBase {

  use ApiTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'media',
    'block',
    'readonly_field_widget',
    'media_library',
    'entity',
    'helfi_tpr',
  ];

  /**
   * The account.
   *
   * @var \Drupal\user\UserInterface
   */
  protected UserInterface $account;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->account = $this->createUser([
      'administer tpr_unit',
      'translate editable entities',
      'view all tpr_unit revisions',
      'revert all tpr_unit revisions',
    ]);
    $this->drupalLogin($this->account);

    $this->drupalPlaceBlock('local_tasks_block');
  }

  /**
   * Migrates the tpr unit entities.
   *
   * @param array $units
   *   The units.
   *
   * @throws \Drupal\migrate\MigrateException
   */
  private function runMigrate(array $units): void {
    $responses = [
      new Response(200, [], json_encode($units)),
    ];

    foreach ($units as $unit) {
      $responses[] = new Response(200, [], json_encode($unit));
    }

    $this->container->set('http_client', $this->createMockHttpClient($responses));
    $this->executeMigration('tpr_unit');
  }

  /**
   * Tests revisions.
   */
  public function testRevision() : void {
    $units = [
      [
        'id' => 999,
        'name_fi' => 'Name fi',
        'name_sv' => 'Name sv',
        'call_charge_info_fi' => 'Charge fi',
        'call_charge_info_sv' => 'Charge sv',
        'modified_time' => '2015-05-16T20:01:01',
      ],
    ];
    $this->runMigrate($units);

    foreach (['fi', 'sv'] as $language) {
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 999]), [
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

      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 999]), [
        'query' => ['language' => $language],
      ]);
      $this->assertSession()->fieldValueEquals('name_override[0][value]', "Override name $language");
      // Make sure TPR data is unchanged when the form is saved.
      $this->assertSession()->pageTextContains("Charge $language");
      $this->assertSession()->pageTextContains("Name $language");

      // Go to revisions tab and make sure it's visible.
      $this->getSession()->getPage()->findLink('Revisions')->click();

      $expected_url = Url::fromRoute('entity.tpr_unit.version_history', ['tpr_unit' => 999], ['query' => ['language' => $language]])->toString();
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

    // Update TPR data and make sure reverting revisions doens't revert TPR
    // fields.
    $units = [
      [
        'id' => 999,
        'name_fi' => 'Name updated fi',
        'name_sv' => 'Name updated sv',
        'call_charge_info_fi' => 'Charge updated fi',
        'call_charge_info_sv' => 'Charge updated sv',
        'modified_time' => '2015-05-16T20:01:01',
      ],
    ];
    $this->runMigrate($units);

    foreach (['fi', 'sv'] as $language) {
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 999]), [
        'query' => ['language' => $language],
      ]);
      // Make sure TPR data is updated.
      $this->assertSession()->pageTextContains("Charge updated $language");
      $this->assertSession()->pageTextContains("Name updated $language");

      // Revert back to first revision.
      $this->getSession()->getPage()->findLink('Revisions')->click();
      $this->getSession()->getPage()->find('css', 'table tr:last-of-type li a')->click();
      $this->getSession()->getPage()->pressButton('Revert');

      // Make sure TPR data is unchanged.
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.edit_form', ['tpr_unit' => 999]), [
        'query' => ['language' => $language],
      ]);
      $this->assertSession()->pageTextContains("Charge updated $language");
      $this->assertSession()->pageTextContains("Name updated $language");
      // Our override field should be empty because we reverted back to the
      // original version.
      $this->assertSession()->fieldValueEquals('name_override[0][value]', '');
    }
  }

}
