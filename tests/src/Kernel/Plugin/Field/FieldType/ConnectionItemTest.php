<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\helfi_tpr\Field\Connection\OpeningHour;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;

/**
 * Tests connection item field.
 *
 * @coversDefaultClass \Drupal\helfi_tpr\Plugin\Field\FieldType\ConnectionItem
 * @group helfi_tpr
 */
class ConnectionItemTest extends FieldKernelTestBase {

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

    // Create a connections field storage and field for validation.
    FieldStorageConfig::create([
      'field_name' => 'field_connections',
      'entity_type' => 'entity_test',
      'type' => 'tpr_connection',
    ])->save();
    FieldConfig::create([
      'entity_type' => 'entity_test',
      'field_name' => 'field_connections',
      'bundle' => 'entity_test',
    ])->save();
  }

  /**
   * Asserts that entity field has expected values.
   *
   * @param \Drupal\entity_test\Entity\EntityTest $entity
   *   The entity to test.
   * @param array $values
   *   The values.
   */
  protected function assertEntity(EntityTest $entity, array $values) : void {
    $object = new OpeningHour();

    foreach ($values as $key => $value) {
      $object->set($key, $value);
    }
    // Make sure we can set values individually.
    $entity->field_connections->value = $values['name'];
    $entity->field_connections->type = 'OPENING_HOURS';
    $entity->field_connections->data = $object;
    $entity->save();

    $entity = EntityTest::load($entity->id());
    $this->assertConnectionField($entity, $object);

    // Make sure we can pass an object to field as well.
    $entity->field_connections = $object;
    $entity->save();

    $entity = EntityTest::load($entity->id());
    $this->assertConnectionField($entity, $object);
  }

  /**
   * Asserts connections field.
   *
   * @param \Drupal\entity_test\Entity\EntityTest $entity
   *   The entity.
   * @param \Drupal\helfi_tpr\Field\Connection\OpeningHour $object
   *   The opening hours object.
   */
  protected function assertConnectionField(EntityTest $entity, OpeningHour $object) : void {
    $this->assertEquals($object->get('name'), $entity->field_connections->value);
    $this->assertEquals($object::TYPE_NAME, $entity->field_connections->type);
    $this->assertEquals($object, $entity->field_connections->data);
  }

  /**
   * Tests connection field type.
   *
   * @covers ::generateSampleValue
   * @covers ::mainPropertyName
   * @covers ::isEmpty
   * @covers ::setValue
   * @covers ::schema
   */
  public function testItem() : void {
    $values = [
      'www' => 'https://localhost/',
      'name' => 'ma-to 10-19',
    ];
    $entity = EntityTest::create();
    $this->assertEntity($entity, $values);
    $this->assertInstanceOf(FieldItemListInterface::class, $entity->field_connections);
    $this->assertInstanceOf(FieldItemInterface::class, $entity->field_connections[0]);

    // Update entity to make sure we can change them.
    $values = [
      'www' => 'https://localhost/1',
      'name' => 'su suljettu',
    ];
    $this->assertEntity($entity, $values);

    // Test sample item generation.
    $entity = EntityTest::create();
    $entity->field_connections->generateSampleItems();
    $this->entityValidateAndSave($entity);
  }

}
