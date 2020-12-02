<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\source;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\helfi_api_base\MigrateTrait;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Plugin\MigrationInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Source plugin for retrieving data from Tpr.
 *
 * @MigrateSource(
 *   id = "tpr"
 * )
 */
class Tpr extends SourcePluginBase implements ContainerFactoryPluginInterface {

  use MigrateTrait;

  /**
   * The number of ignored rows until we stop the migrate.
   *
   * This assumes that your API can be sorted in a way that the newest
   * changes are listed first.
   *
   * For this to have any effect 'track_changes' source setting must be set to
   * true and you must run the migrate with PARTIAL_MIGRATE=1 setting.
   *
   * @var int
   */
  protected const NUM_IGNORED_ROWS_BEFORE_STOPPING = 20;

  /**
   * The http client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected ClientInterface $httpClient;

  /**
   * Keep track of ignored rows to stop migrate after N ignored rows.
   *
   * @var int
   */
  protected int $ignoredRows = 0;

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return 'Tpr';
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return ['id' => ['type' => 'string']];
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function next() {
    parent::next();

    // Check if the current row has changes and increment ignoredRows variable
    // to allow us to stop migrate early if we have no changes.
    if ($this->isPartialMigrate() && $this->currentRow && !$this->currentRow->changed()) {
      $this->ignoredRows++;
    }
  }

  /**
   * Sends a HTTP request and returns response data as array.
   *
   * @param string $url
   *   The url.
   *
   * @return array
   *   The JSON returned by Ahjo service.
   */
  protected function getContent(string $url) : array {
    try {
      $content = (string) $this->httpClient->request('GET', $url)->getBody();
      return \GuzzleHttp\json_decode($content, TRUE);
    }
    catch (GuzzleException $e) {
    }
    return [];
  }

  /**
   * Builds a canonical url to individual entity.
   *
   * @param int $id
   *   The entity ID.
   *
   * @return string
   *   The url to canonical page of given entity.
   */
  private function buildCanonicalUrl(int $id) : string {
    $urlParts = UrlHelper::parse($this->configuration['url']);

    return vsprintf('%s/%s/?%s', [
      rtrim($urlParts['path'], '/'),
      $id,
      UrlHelper::buildQuery($urlParts['query']),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeIterator() {
    $content = $this->getContent($this->configuration['url']);

    $dates = [];
    // Sort data by modified_time.
    foreach ($content as $key => $item) {
      $dates[$key] = DateTimePlus::createFromFormat('Y-m-d\TH:i:s', $item['modified_time'])->format('U');
    }
    array_multisort($dates, SORT_DESC, $content);

    foreach ($content as $object) {
      // Skip entire migration once we've reached the number of maximum
      // ignored (not changed) rows.
      // @see static::NUM_IGNORED_ROWS_BEFORE_STOPPING.
      if ($this->isPartialMigrate() && ($this->ignoredRows >= static::NUM_IGNORED_ROWS_BEFORE_STOPPING)) {
        break;
      }
      $object += $this->getContent($this->buildCanonicalUrl($object['id']));

      yield $object;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MigrationInterface $migration = NULL
  ) {
    $instance = new static($configuration, $plugin_id, $plugin_definition,
      $migration);
    $instance->httpClient = $container->get('http_client');

    if (!isset($configuration['url'])) {
      throw new \InvalidArgumentException('The "url" configuration missing.');
    }
    return $instance;
  }

}
