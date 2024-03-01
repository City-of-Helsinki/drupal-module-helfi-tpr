<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Functional\Plugin\Field\FieldFormatter;

use Drupal\Core\Url;
use Drupal\helfi_tpr\Entity\TprEntityBase;
use Drupal\helfi_tpr\Field\Connection\Highlight;
use Drupal\helfi_tpr\Field\Connection\OpeningHour;

/**
 * Tests connection formatter.
 *
 * @coversDefaultClass \Drupal\helfi_tpr\Plugin\Field\FieldFormatter\ConnectionFormatter
 * @group helfi_tpr
 */
class ConnectionFormatterTest extends CustomFieldFormatterTestBase {

  /**
   * {@inheritdoc}
   */
  protected function getEntity(): TprEntityBase {
    $entity = parent::getEntity();

    foreach (['fi', 'sv', 'en'] as $language) {
      $openingHour = new OpeningHour();
      $openingHour->set('name', "Open $language 1");

      $openingHoursField = $entity->getTranslation($language)
        ->get('opening_hours');
      $openingHoursField->appendItem($openingHour);

      $openingHour = new OpeningHour();
      $openingHour->set('www', "https://localhost/$language")
        ->set('name', "Open $language 2");
      $openingHoursField->appendItem($openingHour);

      $highlight = new Highlight();
      $highlight->set('name', "Highlight $language");
      $highlightsField = $entity->getTranslation($language)
        ->get('highlights');
      $highlightsField->appendItem($highlight);

      $entity->save();
    }

    return $entity;
  }

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
        'Opening hours',
        "Open $language 1",
        "Open $language 2",
        "Highlight $language",
      ];

      foreach ($strings as $string) {
        $expect_visible ?
          $this->assertSession()->pageTextContains($string) :
          $this->assertSession()->pageTextNotContains($string);
      }

      $expect_visible ?
        $this->assertSession()->linkExists("Open $language 2") :
        $this->assertSession()->linkNotExists("Open $language 2");
    }
  }

  /**
   * Tests the formatter.
   *
   * @covers ::viewElements
   */
  public function testFormatter() : void {
    // Make sure field display is disabled by default.
    $this->assertFieldDisplay(FALSE);

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getViewDisplay('tpr_unit', 'tpr_unit')
      ->setComponent('opening_hours', [
        'type' => 'tpr_connection',
        'label' => 'above',
      ])
      ->setComponent('highlights', [
        'type' => 'tpr_connection',
        'label' => 'above',
      ])
      ->save();

    // Make sure we can see connections field for all languages once
    // the field display is enabled.
    $this->assertFieldDisplay(TRUE);
  }

}
