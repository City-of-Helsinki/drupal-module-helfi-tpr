<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\SortArray;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\helfi_tpr\Entity\ChannelTypeCollection;
use Drupal\helfi_tpr\Entity\ErrandService;
use Drupal\helfi_tpr\Entity\Service;

/**
 * Field formatter to render errand service maps.
 *
 * @FieldFormatter(
 *   id = "tpr_errand_servie_formatter",
 *   label = @Translation("TPR - Errand Service formatter"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */

class ErrandServicesFormatter extends EntityReferenceEntityFormatter {
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
      throw new \InvalidArgumentException('The field can only be used with tpr_errand_service entities.');
    }
    $elements = parent::viewElements($items, $langcode);
    $channelTypes = $this->getChannelTypes();

    foreach ($elements as $delta => $element) {
      /** @var \Drupal\helfi_tpr\Entity\ErrandService $entity */
      $entity = $element['#tpr_errand_service'];
      foreach ($entity->getChannels() as $channel) {
        $elements[$delta]['#weight'] = $channelTypes[$channel->getType()]->weight;
      }

    }
    uasort($elements, [SortArray::class, 'sortByWeightProperty']);
    return $elements;
  }
}
