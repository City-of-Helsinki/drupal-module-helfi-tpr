<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\Channel;
use Drupal\helfi_tpr\Entity\ErrandService;

/**
 * Tests TPR Errand service migration.
 *
 * @group helfi_tpr
 */
class ErrandServiceMigrationTest extends MigrationTestBase {

  /**
   * Tests that default values are not overridden by migrate.
   */
  public function testDefaultErrandServiceValues() : void {
    $this->runErrandServiceMigration();
    $entities = ErrandService::loadMultiple();

    // Update translation author and status fields.
    $translation = $entities[1]->getTranslation('sv');

    $this->assertEquals('1', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('0', $translation->get('content_translation_status')->value);

    $translation->set('content_translation_uid', '0')
      ->set('content_translation_status', TRUE)
      ->save();

    // Re-run migrate and make sure author and status fields won't get updated.
    $this->runErrandServiceMigration();
    $entities = ErrandService::loadMultiple();
    $translation = $entities[1]->getTranslation('sv');

    $this->assertEquals('0', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('1', $translation->get('content_translation_status')->value);
  }

  /**
   * Tests errand service migration.
   */
  public function testErrandServiceMigration() : void {
    $this->runErrandServiceMigration();
    $entities = ErrandService::loadMultiple();
    $this->assertCount(3, $entities);

    foreach (['fi', 'sv', 'en'] as $langcode) {
      foreach ($entities as $entity) {
        $translation = $entity->getTranslation($langcode);

        $this->assertEquals($langcode, $translation->language()->getId());

        foreach ($this->fixture('tpr_errand_service')->getFields() as $field) {
          $this->assertEquals(
            sprintf('%s %s %s', $field, $langcode, $translation->id()),
            $translation->get($field)->value
          );
        }

        for ($i = 0; $i < 2; $i++) {
          /** @var \Drupal\link\Plugin\Field\FieldType\LinkItem $link */
          $link = $translation->get('links')->get($i);
          $this->assertEquals(sprintf('%s:%s link title %s', $i, $langcode, $translation->id()), $link->title);
          $this->assertEquals(sprintf('https://localhost/%s/%s/%s', $i, $langcode, $translation->id()), $link->uri);
        }
      }
    }
  }

  /**
   * Tests that default values are not overridden by migrate.
   */
  public function testDefaultServiceChannelValues() : void {
    $this->runErrandServiceMigration();
    $this->executeMigration('tpr_service_channel');
    $entities = Channel::loadMultiple();
    $this->assertCount(3, $entities);

    // Update translation author and status fields.
    $translation = $entities[1]->getTranslation('sv');

    $this->assertEquals('1', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('0', $translation->get('content_translation_status')->value);

    $translation->set('content_translation_uid', '0')
      ->set('content_translation_status', TRUE)
      ->save();

    // Re-run migrate and make sure author and status fields won't get updated.
    $this->executeMigration('tpr_service_channel');
    $entities = Channel::loadMultiple();
    $translation = $entities[1]->getTranslation('sv');

    $this->assertEquals('0', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('1', $translation->get('content_translation_status')->value);
  }

  /**
   * Tests service channel migration.
   */
  public function testServiceChannelMigration() : void {
    $this->runErrandServiceMigration();
    $this->executeMigration('tpr_service_channel');
    $entities = Channel::loadMultiple();
    $this->assertCount(3, $entities);

    foreach (['fi', 'sv', 'en'] as $langcode) {
      foreach ($entities as $entity) {
        $translation = $entity->getTranslation($langcode);

        $channel_fields = [
          'type_string' => sprintf('email %s %s', $langcode, $translation->id()),
          'email' => sprintf('email.%s.%s@hel.fi', $langcode, $translation->id()),
          'phone' => sprintf('123456-%s-%s', $langcode, $translation->id()),
          'call_charge_info' => sprintf('call_charge_info %s %s', $langcode, $translation->id()),
          'type' => 'EMAIL',
          'requires_authentication' => TRUE,
          'saved_to_customer_folder' => TRUE,
          'e_processing' => TRUE,
          'e_decision' => TRUE,
          'payment_enabled' => TRUE,
          'for_personal_customer' => TRUE,
          'for_corporate_customer' => TRUE,
        ];

        foreach ($channel_fields as $field => $expected) {
          $this->assertEquals($expected, $translation->get($field)->value);
        }

        for ($i = 0; $i < 2; $i++) {
          /** @var \Drupal\link\Plugin\Field\FieldType\LinkItem $link */
          $link = $translation->get('links')->get($i);
          $this->assertEquals(sprintf('%s:%s link title %s', $i, $langcode, $translation->id()), $link->title);
          $this->assertEquals(sprintf('https://localhost/%s/%s/%s', $i, $langcode, $translation->id()), $link->uri);
          $this->assertEquals(sprintf('%s:test %s %s', $i, $langcode, $translation->id()), $translation->get('availabilities')->get($i)->value);
        }
      }
    }
  }

}
