<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\SortArray;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\helfi_tpr\Entity\ChannelTypeCollection;
use Drupal\helfi_tpr\Entity\Service;

/**
 * Field formatter to render errand service maps.
 *
 * @FieldFormatter(
 *   id = "tpr_service_err_channel_list",
 *   label = @Translation("TPR - Errand Service Channels List"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */

class ErrandServicesFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'sort_order' => [],
      ] + parent::defaultSettings();
  }

  /**
   * Gets the channel types.
   *
   * @return \Drupal\helfi_tpr\Entity\ChannelTypeCollection
   *   The channel type collection.
   */
  private function getChannelTypes() : ChannelTypeCollection {
    return ChannelTypeCollection::createFromArray($this->getSetting('sort_order'));
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['sort_order'] = [
      '#type' => 'table',
      '#caption' => $this->t('Order'),
      '#header' => [
        $this->t('ID'),
        $this->t('Label'),
        $this->t('Weight'),
      ],
      '#tableselect' => FALSE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'group-weight',
        ],
      ],
    ];

    foreach ($this->getChannelTypes() as $item) {
      $form['sort_order'][$item->id] = [
        '#attributes' => ['class' => ['draggable']],
        '#weight' => $item->weight,
        'id' => ['#plain_text' => $item->id],
        'label' => ['#plain_text' => $item->label()],
        'weight' => [
          '#type' => 'weight',
          '#title' => $this->t('Weight for @title', ['@title' => $item->label()]),
          '#title_display' => 'invisible',
          '#default_value' => $item->weight,
          '#attributes' => ['class' => ['group-weight']],
        ],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) : array {

    if (!$items->getEntity() instanceof Service) {
      throw new \InvalidArgumentException('The field can only be used with Services entities.');
    }

    $channelTypes = $this->getChannelTypes();

    /** @var \Drupal\Core\Render\Renderer $renderer */
    $renderer = \Drupal::service('renderer');

    $channel_list = [];
    $errand_services = $items->referencedEntities();
    $item_list = [
      '#theme' => 'item_list',
      '#items' => [],
    ];

    foreach ($errand_services as $errand_service) {
      /** @var \Drupal\helfi_tpr\Entity\ErrandService $errand_service */
      foreach ($errand_service->getChannels() as $channel) {
        if (isset($channel_list[$channel->getType()])) {
          continue;
        }

        /** @var \Drupal\helfi_tpr\Entity\Channel $translatedChannel */
        $translatedChannel = \Drupal::service('entity.repository')->getTranslationFromContext($channel, $langcode);
        $channel_list[$channel->getType()] = [
          '#name' => $translatedChannel->type_string->value,
          '#weight' => $channelTypes[$channel->getType()]->weight,
        ];
        $renderer->addCacheableDependency($item_list, $translatedChannel);
      }
    }

    /** @var \Drupal\helfi_tpr\Entity\Service $service */
    $service = $items->getEntity();

    if ($service->hasField('has_unit') && $service->has_unit->value) {
      $channel_list['OFFICE'] = [
        '#name' => $this->t('Office'),
        '#weight' => 999,
      ];
    }

    uasort($channel_list, [SortArray::class, 'sortByWeightProperty']);
    $item_list['#items'] = array_column($channel_list, '#name');

    $elements[0] = $item_list;

    return $elements;
  }

}
