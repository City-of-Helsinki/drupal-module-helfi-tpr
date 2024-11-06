<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\SortArray;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\helfi_tpr\Entity\ChannelType;
use Drupal\helfi_tpr\Entity\ChannelTypeCollection;
use Drupal\helfi_tpr\Entity\Service;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
final class ErrandServicesChannelFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  private RendererInterface $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ) : self {
    $instance = parent::create($container, $configuration, $plugin_id,
      $plugin_definition);
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() : array {

    return [
      'sort_order' => [],
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() : array {
    $settings = $this->getSetting('sort_order');
    $selected_channels = [];
    foreach ($settings as $channel_type => $channel_setting) {
      if ($channel_setting['show']) {
        $selected_channels[] = $channel_type;
      }
    }

    return [
      (string) $this->t('Showing @list', [
        '@list' => implode(', ', $selected_channels),
      ]),
    ];
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
  public function settingsForm(array $form, FormStateInterface $form_state) : array {
    $form = parent::settingsForm($form, $form_state);

    $form['sort_order'] = [
      '#type' => 'table',
      '#caption' => $this->t('Order'),
      '#header' => [
        $this->t('ID'),
        $this->t('Label'),
        $this->t('Show'),
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
    $channelTypes = [];
    foreach ($this->getChannelTypes() as $channelId => $channelType) {
      $channelTypes[$channelId] = $channelType;
    }
    $channelTypes['OFFICE'] = new ChannelType('OFFICE', 99);

    foreach ($channelTypes as $item) {
      $form['sort_order'][$item->id] = [
        '#attributes' => ['class' => ['draggable']],
        '#weight' => $item->weight,
        'id' => ['#plain_text' => $item->id],
        'label' => [
          '#type' => 'textfield',
          '#default_value' => $this->getSetting('sort_order')[$item->id]['label'] ?? $item->id,
        ],
        'show' => [
          '#type' => 'checkbox',
          '#default_value' => $this->getSetting('sort_order')[$item->id]['show'] ?? 0,
        ],
        'weight' => [
          '#type' => 'weight',
          '#title' => $this->t('Weight for @title', ['@title' => $item->id]),
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

    $channel_list = [];
    /** @var \Drupal\helfi_tpr\Entity\ErrandService[] $errand_services */
    $errand_services = $items->referencedEntities();
    $item_list = [
      '#theme' => 'item_list',
      '#items' => [],
    ];

    foreach ($errand_services as $errand_service) {
      foreach ($errand_service->getChannels() as $channel) {
        if (isset($channel_list[$channel->getType()])
        || empty($this->getSetting('sort_order')[$channel->getType()]['show'])) {
          continue;
        }

        $channel_list[$channel->getType()] = [
          '#name' => $this->getSetting('sort_order')[$channel->getType()]['label'],
          '#weight' => $channelTypes[$channel->getType()]->weight,
        ];
        $this->renderer->addCacheableDependency($item_list, $channel);
      }
    }

    /** @var \Drupal\helfi_tpr\Entity\Service $service */
    $service = $items->getEntity();

    if ($service->hasField('has_unit') && $service->has_unit->value) {
      $channel_list['OFFICE'] = [
        '#name' => $this->getSetting('sort_order')['OFFICE']['label'],
        '#weight' => $this->getSetting('sort_order')['OFFICE']['weight'],
      ];
    }

    uasort($channel_list, [SortArray::class, 'sortByWeightProperty']);
    $item_list['#items'] = array_column($channel_list, '#name');

    $elements[0] = $item_list;

    return $elements;
  }

}
