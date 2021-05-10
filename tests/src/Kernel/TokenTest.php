<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\file\Entity\File;
use Drupal\helfi_tpr\Entity\Unit;
use Drupal\media\Entity\Media;
use Drupal\Tests\media\Traits\MediaTypeCreationTrait;

/**
 * Tests custom tokens.
 */
class TokenTest extends MigrationTestBase {

  use MediaTypeCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'file',
    'image',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('media');
    $this->installEntitySchema('file');
    $this->installSchema('file', 'file_usage');
    $this->installEntitySchema('media');
    $this->installConfig(['field', 'system', 'image', 'file', 'media']);
    $this->installConfig(['media', 'file']);

    $this->createMediaType('image', ['id' => 'image']);
  }

  /**
   * Tests label token.
   */
  public function testLabel() : void {
    $entity_types = [
      'tpr_unit',
      'tpr_service',
      'tpr_service_channel',
      'tpr_errand_service',
    ];

    $entities = [];

    foreach ($entity_types as $entity_type) {
      $storage = \Drupal::entityTypeManager()->getStorage($entity_type);
      /** @var \Drupal\helfi_tpr\Entity\TprEntityBase $entity */
      $entity = $storage->create([
        'id' => 999,
        'name' => 'Name fi',
        'langcode' => 'fi',
      ]);
      $entity
        ->addTranslation('en', [
          'name' => "Name en",
        ])
        ->addTranslation('sv', [
          'name' => 'Name sv',
        ]);
      $entity->save();

      $entities[$entity_type] = $entity;
    }

    $this->assertCount(4, $entities);

    foreach ($entities as $entity_type => $entity) {
      foreach (['fi', 'en', 'sv'] as $language) {
        $entity = $entity->getTranslation($language);
        $label = \Drupal::token()->replace(sprintf('[%s:label]', $entity_type), [$entity_type => $entity]);
        $this->assertEquals("Name $language", $label);

        // Set name override and make sure token gets updated.
        $entity->set('name_override', "Name $language override")->save();
        $label = \Drupal::token()->replace(sprintf('[%s:label]', $entity_type), [$entity_type => $entity]);
        $this->assertEquals("Name $language override", $label);
      }
    }
  }

  /**
   * Tests description token.
   */
  public function testDescription() : void {
    $unit = Unit::create([
      'id' => 999,
      'name' => 'Name fi',
      'langcode' => 'fi',
      'description' => [
        'value' => 'Description fi value',
        'summary' => 'Description fi summary',
        'format' => 'plain_text',
      ],
    ]);
    $unit
      ->addTranslation('en', [
        'description' => [
          'value' => 'Description en value',
          'summary' => 'Description en summary',
          'format' => 'plain_text',
        ],
      ])
      ->addTranslation('sv', [
        'description' => [
          'value' => 'Description sv value',
          'summary' => 'Description sv summary',
          'format' => 'plain_text',
        ],
      ])
      ->save();

    foreach (['en', 'sv', 'fi'] as $langcode) {
      foreach (['value', 'summary'] as $type) {
        $description = \Drupal::token()
          ->replace("[tpr_unit:description:$type]", [
            'tpr_unit' => $unit->getTranslation($langcode),
          ]);
        $this->assertEquals("Description $langcode $type", $description);
      }
    }
  }

  /**
   * Tests picture token.
   */
  public function testPictureUrl() : void {
    $unit = Unit::create([
      'id' => 999,
      'name' => 'Name fi',
      'langcode' => 'fi',
      'picture_url' => 'http://localhost/image.png',
    ]);
    // Picture url field is not actually translatable.
    $unit->addTranslation('en')
      ->addTranslation('sv')
      ->save();

    foreach (['en', 'sv', 'fi'] as $langcode) {
      $picture = \Drupal::token()
        ->replace('[tpr_unit:picture]', [
          'tpr_unit' => $unit->getTranslation($langcode),
        ]);
      $this->assertEquals('http://localhost/image.png', $picture);
    }

    $file = File::create([
      'uri' => 'public://image_override.png',
      'uid' => 1,
    ]);
    $file->setPermanent();
    $file->save();

    $media = Media::create([
      'bundle' => 'image',
      'name' => 'Test',
      'field_media_image' => [
        'target_id' => $file->id(),
      ],
    ]);
    $unit->set('picture_url_override', $media)->save();

    // Make sure picture token url changed to override picture.
    foreach (['en', 'sv', 'fi'] as $langcode) {
      $picture = \Drupal::token()
        ->replace('[tpr_unit:picture]', [
          'tpr_unit' => $unit->getTranslation($langcode),
        ]);
      // We don't care about the whole url, just the filename.
      $this->assertStringContainsString('http://localhost', $picture);
      $this->assertStringContainsString('files/image_override.png', $picture);
    }
  }

}
