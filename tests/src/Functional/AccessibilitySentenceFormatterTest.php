<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Core\Url;
use Drupal\helfi_tpr\Entity\Unit;
use Drupal\Tests\helfi_api_base\Functional\MigrationTestBase;

/**
 * Tests accessibility sentence formatter.
 *
 * @group helfi_tpr
 */
class AccessibilitySentenceFormatterTest extends MigrationTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field',
    'helfi_tpr',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The entity to test.
   *
   * @var \Drupal\helfi_tpr\Entity\Unit
   */
  protected Unit $entity;

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();

    $account = $this->createUser([
      'view tpr_unit',
    ]);
    $this->drupalLogin($account);

    $this->entity = Unit::create([
      'id' => 999,
      'langcode' => 'fi',
      'name' => 'Test',
    ]);

    $this->entity->addTranslation('sv', ['name' => 'Test'])
      ->addTranslation('en', ['name' => 'Test'])
      ->save();

    foreach (['fi', 'sv', 'en'] as $language) {
      for ($i = 1; $i <= 2; $i++) {
        $this->entity->getTranslation($language)
          ->get('accessibility_sentences')
          ->appendItem([
            'group' => "Test group $language $i",
            'value' => "Test value $language $i",
          ]);
        $this->entity->save();
      }
    }
  }

  /**
   * Asserts that we can or can't see the field values.
   *
   * @param bool $expected
   *   Whether we should see field values.
   */
  private function assertFieldDisplay(bool $expected) : void {
    foreach (['fi', 'en', 'sv'] as $language) {
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.canonical', ['tpr_unit' => 999]), [
        'query' => ['language' => $language],
      ]);

      $strings = [
        'Accessibility sentences',
        "Test group $language 1",
        "Test value $language 1",
        "Test value $language 2",
      ];

      foreach ($strings as $string) {
        $expected ?
          $this->assertSession()->pageTextContains($string) :
          $this->assertSession()->pageTextNotContains($string);
      }
    }
  }

  /**
   * Tests the formatter.
   */
  public function testFormatter() : void {
    // Make sure field display is disabled by default.
    $this->assertFieldDisplay(FALSE);

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getViewDisplay('tpr_unit', 'tpr_unit')
      ->setComponent('accessibility_sentences', [
        'type' => 'accessibility_sentence',
        'label' => 'above',
      ])
      ->save();

    // Make sure we can see accessibility sentences for all languages once
    // the field display is enabled.
    $this->assertFieldDisplay(TRUE);
  }

}
