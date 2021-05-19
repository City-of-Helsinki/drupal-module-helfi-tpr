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

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getViewDisplay('tpr_unit', 'tpr_unit')
      ->setComponent('accessibility_sentence', [
        'type' => 'accessibility_sentence',
        'weight' => 1,
        'label' => 'above',
      ])
      ->save();
  }

  /**
   * Tests the formatter.
   */
  public function testFormatter() : void {
    // Make sure we can see accessibility sentences for all languages.
    foreach (['fi', 'en', 'sv'] as $language) {
      $this->drupalGet(Url::fromRoute('entity.tpr_unit.canonical', ['tpr_unit' => 999]), [
        'query' => ['language' => $language],
      ]);
      $this->assertSession()->pageTextContains('Accessibility sentences');
      $this->assertSession()->pageTextContains("Test group $language 1");
      $this->assertSession()->pageTextContains("Test value $language 1");
      $this->assertSession()->pageTextContains("Test group $language 2");
      $this->assertSession()->pageTextContains("Test value $language 2");
    }
  }

}
