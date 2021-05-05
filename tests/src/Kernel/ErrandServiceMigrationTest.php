<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_tpr\Entity\Channel;
use Drupal\helfi_tpr\Entity\ErrandService;
use GuzzleHttp\Psr7\Response;

/**
 * Tests TPR Errand service migration.
 *
 * @group helfi_tpr
 */
class ErrandServiceMigrationTest extends MigrationTestBase {

  /**
   * Gets the fields used in migration.
   *
   * @return string[]
   *   The fields.
   */
  protected function getFields() : array {
    return [
      'name',
      'description',
      'process_description',
      'processing_time',
      'information',
      'expiration_time',
      'costs',
    ];
  }

  /**
   * Create the errand service migration.
   *
   * @return array
   *   List of entities.
   */
  protected function createErrandServiceMigration() : array {
    $eservices = [
      ['id' => 1],
      ['id' => 2],
      ['id' => 3],
    ];
    $responses = [
      new Response(200, [], json_encode($eservices)),
    ];

    foreach ($eservices as $service) {
      $id = $service['id'];

      foreach (['fi', 'en', 'sv'] as $language) {
        $service['links'] = [
          [
            'title' => sprintf('0:%s link title %s', $language, $id),
            'url' => sprintf('https://localhost/0/%s/%s', $language, $id),
          ],
          [
            'title' => sprintf('1:%s link title %s', $language, $id),
            'url' => sprintf('https://localhost/1/%s/%s', $language, $id),
          ],
        ];

        $service['channels'] = [
          [
            'id' => $id,
            'type_string' => sprintf('email %s %s', $language, $id),
            'email' => sprintf('email.%s.%s@hel.fi', $language, $id),
            'phone' => sprintf('123456-%s-%s', $language, $id),
            'call_charge_info' => sprintf('call_charge_info %s %s', $language, $id),
            'availabilities' => [
              [
                'availability_string' => sprintf('0:test %s %s', $language, $id),
              ],
              [
                'availability_string' => sprintf('1:test %s %s', $language, $id),
              ],
            ],
            'links' => [
              [
                'title' => sprintf('0:%s link title %s', $language, $id),
                'url' => sprintf('https://localhost/0/%s/%s', $language, $id),
              ],
              [
                'title' => sprintf('1:%s link title %s', $language, $id),
                'url' => sprintf('https://localhost/1/%s/%s', $language, $id),
              ],
            ],
            'type' => 'EMAIL',
            'requires_authentication' => TRUE,
            'saved_to_customer_folder' => TRUE,
            'e_processing' => TRUE,
            'e_decision' => TRUE,
            'payment_enabled' => TRUE,
            'for_personal_customer' => TRUE,
            'for_corporate_customer' => TRUE,
          ],
        ];

        foreach ($this->getFields() as $field) {
          $service[$field] = sprintf('%s %s %s', $field, $language, $id);
        }
        $responses[] = new Response(200, [], json_encode($service));
      }
    }

    $this->container->set('http_client', $this->createMockHttpClient($responses));
    $this->executeMigration('tpr_errand_service');
    return ErrandService::loadMultiple();
  }

  /**
   * Tests that default values are not overridden by migrate.
   */
  public function testDefaultErrandServiceValues() : void {
    $entities = $this->createErrandServiceMigration();

    // Update translation author and status fields.
    $translation = $entities[1]->getTranslation('sv');

    $this->assertEquals('1', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('0', $translation->get('content_translation_status')->value);

    $translation->set('content_translation_uid', '0')
      ->set('content_translation_status', TRUE)
      ->save();

    // Re-run migrate and make sure author and status fields won't get updated.
    $entities = $this->createErrandServiceMigration();
    $translation = $entities[1]->getTranslation('sv');

    $this->assertEquals('0', $translation->get('content_translation_uid')->target_id);
    $this->assertequals('1', $translation->get('content_translation_status')->value);
  }

  /**
   * Tests errand service migration.
   */
  public function testErrandServiceMigration() : void {
    $entities = $this->createErrandServiceMigration();
    $this->assertCount(3, $entities);

    foreach (['fi', 'sv', 'en'] as $langcode) {
      foreach ($entities as $entity) {
        $translation = $entity->getTranslation($langcode);

        $this->assertEquals($langcode, $translation->language()->getId());

        foreach ($this->getFields() as $field) {
          $this->assertEquals(
            sprintf('%s %s %s', $field, $langcode, $translation->id()),
            $translation->get($field)->value
          );
        }

        for ($i = 0; $i < 2; $i++) {
          $this->assertEquals(sprintf('%s:%s link title %s', $i, $langcode, $translation->id()), $translation->get('links')->get($i)->title);
          $this->assertEquals(sprintf('https://localhost/%s/%s/%s', $i, $langcode, $translation->id()), $translation->get('links')->get($i)->uri);
        }
      }
    }
  }

  /**
   * Tests that default values are not overridden by migrate.
   */
  public function testDefaultServiceChannelValues() : void {
    $this->createErrandServiceMigration();
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
    $this->createErrandServiceMigration();

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
          $this->assertEquals(sprintf('%s:%s link title %s', $i, $langcode, $translation->id()), $translation->get('links')->get($i)->title);
          $this->assertEquals(sprintf('https://localhost/%s/%s/%s', $i, $langcode, $translation->id()), $translation->get('links')->get($i)->uri);
          $this->assertEquals(sprintf('%s:test %s %s', $i, $langcode, $translation->id()), $translation->get('availabilities')->get($i)->value);
        }
      }
    }
  }

}
