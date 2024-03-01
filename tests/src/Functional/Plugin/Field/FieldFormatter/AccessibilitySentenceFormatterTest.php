<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Functional\Plugin\Field\FieldFormatter;

use Drupal\Core\Url;
use Drupal\helfi_tpr\Entity\TprEntityBase;

/**
 * Tests accessibility sentence formatter.
 *
 * @coversDefaultClass \Drupal\helfi_tpr\Plugin\Field\FieldFormatter\AccessibilitySentenceFormatter
 * @group helfi_tpr
 */
class AccessibilitySentenceFormatterTest extends CustomFieldFormatterTestBase {

  /**
   * Asserts that we can or can't see the field values.
   *
   * @param bool $expect_visible
   *   Whether we should see field values.
   */
  private function assertFieldDisplay(bool $expect_visible) : void {
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
        $expect_visible ?
          $this->assertSession()->pageTextContains($string) :
          $this->assertSession()->pageTextNotContains($string);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntity(): TprEntityBase {
    $entity = parent::getEntity();

    foreach (['fi', 'sv', 'en'] as $language) {
      for ($i = 1; $i <= 2; $i++) {
        $entity->getTranslation($language)
          ->get('accessibility_sentences')
          ->appendItem([
            'group' => "Test group $language $i",
            'value' => "Test value $language $i",
          ]);
        $entity->save();
      }
    }

    return $entity;
  }

  /**
   * Tests the formatter.
   *
   * @covers ::viewElements
   * @covers ::groupItemsByLabel
   */
  public function testFormatter() : void {
    // Make sure field display is disabled by default.
    $this->assertFieldDisplay(FALSE);

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getViewDisplay('tpr_unit', 'tpr_unit')
      ->setComponent('accessibility_sentences', [
        'type' => 'tpr_accessibility_sentence',
        'label' => 'above',
      ])
      ->save();

    // Make sure we can see accessibility sentences for all languages once
    // the field display is enabled.
    $this->assertFieldDisplay(TRUE);
  }

}
