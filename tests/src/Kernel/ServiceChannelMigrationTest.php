<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\helfi_tpr\Entity\Channel;
use Drupal\helfi_tpr\Entity\ErrandService;

/**
 * Tests TPR service channel migration.
 *
 * @group helfi_tpr
 */
class ServiceChannelMigrationTest extends MigrationTestBase {

  /**
   * Tests that default values are not overridden by migrate.
   */
  public function testDefaultServiceChannelValues() : void {
    $this->runServiceChannelMigration();
    $entities = Channel::loadMultiple();
    $this->assertCount(6, $entities);

    // Update translation author and status fields.
    $translation = reset($entities)->getTranslation('sv');

    $this->assertEquals('1', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('0', $translation->get('content_translation_status')->value);

    $translation->set('content_translation_uid', '0')
      ->set('content_translation_status', TRUE)
      ->save();

    // Re-run migrate and make sure author and status fields won't get updated.
    $this->runServiceChannelMigration();
    $entities = Channel::loadMultiple();
    $translation = reset($entities)->getTranslation('sv');

    $this->assertEquals('0', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('1', $translation->get('content_translation_status')->value);
  }

  /**
   * Tests service channel migration.
   */
  public function testServiceChannelMigration() : void {
    $this->runErrandServiceMigration();
    $this->runServiceChannelMigration();
    $entities = ErrandService::loadMultiple();
    $this->assertCount(3, $entities);

    foreach (['fi', 'sv', 'en'] as $langcode) {
      foreach ($entities as $entity) {
        /** @var \Drupal\helfi_tpr\Entity\ErrandService $translation */
        $channels = $entity->getTranslation($langcode)->getChannels();
        $this->assertCount(2, $channels);

        foreach ($channels as $channel) {
          /** @var \Drupal\helfi_tpr\Entity\Channel $translation */
          $translation = $channel->getTranslation($langcode);

          $args = [
            '@id' => $entity->id(),
            '@langcode' => $langcode,
            '@tid' => $translation->id(),
          ];

          $channel_fields = [
            'type_string' => new FormattableMarkup('@id email @langcode @tid', $args),
            'email' => new FormattableMarkup('@id.email.@langcode.@tid@hel.fi', $args),
            'phone' => new FormattableMarkup('@id-123456-@langcode-@tid', $args),
            'call_charge_info' => new FormattableMarkup('call_charge_info @id @langcode @tid', $args),
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
            $args['@index'] = $i;

            /** @var \Drupal\link\Plugin\Field\FieldType\LinkItem $link */
            $link = $translation->get('links')->get($i);
            $this->assertEquals(new FormattableMarkup('@index:@id:@langcode link title @tid', $args), $link->title);
            $this->assertEquals(new FormattableMarkup('https://localhost/@index/@id/@langcode/@tid', $args), $link->uri);
            $this->assertEquals(new FormattableMarkup('@index:@id:test @langcode @tid', $args), $translation->get('availabilities')->get($i)->value);
          }
        }

      }
    }
  }

}
