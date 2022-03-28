<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Core\Url;
use Drupal\helfi_tpr\Entity\Channel;
use Drupal\helfi_tpr\Entity\ChannelType;
use Drupal\helfi_tpr\Entity\ChannelTypeCollection;
use Drupal\helfi_tpr\Entity\ErrandService;
use Drupal\helfi_tpr\Entity\Service;
use Drupal\helfi_tpr\Entity\TprEntityBase;

/**
 * Tests service channel formatter.
 *
 * @group helfi_tpr
 */
class ServiceChannelFormatterTest extends CustomFieldFormatterTestBase {

  /**
   * {@inheritdoc}
   */
  protected function getUserPermissions(): array {
    $permissions = parent::getUserPermissions();
    return $permissions + [
      'access content',
      'view remote entities',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntity() : TprEntityBase {
    $errandService = ErrandService::create([
      'id' => 1,
      'name' => 'E-service',
      'langcode' => 'fi',
    ]);
    $errandService
      ->setPublished()
      ->save();

    $channelTypes = ChannelTypeCollection::all();
    $id = 1;
    foreach ($channelTypes as $type) {
      $channel = Channel::create([
        'id' => $id++,
        'name' => sprintf('Channel %s', $type->id),
        'type' => $type->id,
        'langcode' => 'fi',
      ]);
      $channel->setPublished()
        ->save();
      $errandService->addChannel($channel);
    }
    $errandService->save();

    $entity = Service::create([
      'id' => 999,
      'langcode' => 'fi',
      'name' => 'Test service',
    ]);
    $entity->addErrandService($errandService)
      ->setPublished()
      ->save();

    return $entity;
  }

  /**
   * Asserts that channels are sorted in given order.
   *
   * @param array $expectedOrder
   *   The expected order.
   */
  private function assertChannelOrder(array $expectedOrder) : void {
    foreach ($expectedOrder as $id => $element) {
      $selector = sprintf('.field--name-channels div.field__item:nth-of-type(%d) .field--name-type', $element['weight']);

      $item = $this->getSession()
        ->getPage()
        ->find('css', $selector);
      $this->assertEquals($id, $item?->getText());
    }
  }

  /**
   * Tests the formatter.
   */
  public function testFormatter() : void {
    $this->drupalGet(Url::fromRoute('entity.tpr_service.canonical', ['tpr_service' => 999]), [
      'query' => ['language' => 'fi'],
    ]);

    $channelTypes = ChannelTypeCollection::all();

    // Make sure all channels are visible and sorted in same order as they are
    // defined in ChannelTypeCollection.
    $index = 1;
    $expectedOrder = [];
    foreach ($channelTypes as $type) {
      $expectedOrder[$type->id] = ['weight' => $index++];
      $this->assertSession()->pageTextContains(sprintf('Channel %s', $type->id));
    }
    $this->assertChannelOrder($expectedOrder);

    // Sort channels by name, then test that changing the order in
    // widget settings changes the order in TPR service display.
    $channelsSortedByName = iterator_to_array($channelTypes);
    usort($channelsSortedByName, function (ChannelType $a, ChannelType $b) {
      return strnatcmp($a->id, $b->id);
    });

    $index = 1;
    $expectedOrder = [];
    foreach ($channelsSortedByName as $item) {
      $expectedOrder[$item->id] = ['weight' => $index++];
    }

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getViewDisplay('tpr_errand_service', 'tpr_errand_service')
      ->setComponent('channels', [
        'type' => 'tpr_service_channel_formatter',
        'settings' => [
          'sort_order' => $expectedOrder,
        ],
      ])
      ->save();

    $this->drupalGet(Url::fromRoute('entity.tpr_service.canonical', ['tpr_service' => 999]), [
      'query' => ['language' => 'fi'],
    ]);
    // Make sure channels are ordered by name.
    $this->assertChannelOrder($expectedOrder);
  }

}
