<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;

/**
 * Tests accessibility sentence field.
 *
 * @group helfi_tpr
 */
class AccessibilitySentenceItemTest extends FieldKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'helfi_tpr',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {
    parent::setUp();

    // Create a telephone field storage and field for validation.
    FieldStorageConfig::create([
      'field_name' => 'field_accessibility_sentences',
      'entity_type' => 'entity_test',
      'type' => 'tpr_accessibility_sentence',
    ])->save();
    FieldConfig::create([
      'entity_type' => 'entity_test',
      'field_name' => 'field_accessibility_sentences',
      'bundle' => 'entity_test',
    ])->save();
  }

  /**
   * Asserts that entity field has expected values.
   *
   * @param \Drupal\entity_test\Entity\EntityTest $entity
   *   The entity to test.
   * @param string $expected_group
   *   The expected group.
   * @param string $expected_value
   *   The expected value.
   */
  protected function assertEntity(EntityTest $entity, string $expected_group, string $expected_value) : void {
    $entity->field_accessibility_sentences->group = $expected_group;
    $entity->field_accessibility_sentences->value = $expected_value;
    $entity->save();

    $entity = EntityTest::load($entity->id());
    $this->assertEquals($expected_group, $entity->field_accessibility_sentences->group);
    $this->assertEquals($expected_value, $entity->field_accessibility_sentences->value);
  }

  /**
   * Tests accessibility sentences field type.
   */
  public function testItem() : void {
    $entity = EntityTest::create();
    $this->assertEntity($entity, 'Test group', 'Test value');
    $this->assertInstanceOf(FieldItemListInterface::class, $entity->field_accessibility_sentences);
    $this->assertInstanceOf(FieldItemInterface::class, $entity->field_accessibility_sentences[0]);

    // Update entity to make sure we can change them.
    $this->assertEntity($entity, 'New test group', 'New test value');

    // Test sample item generation.
    $entity = EntityTest::create();
    $entity->field_accessibility_sentences->generateSampleItems();
    $this->entityValidateAndSave($entity);
  }

}
