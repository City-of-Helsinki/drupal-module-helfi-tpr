<?php

declare(strict_types = 1);

namespace Drupal\helfi_address_search\Plugin\views\filter;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\views\Plugin\views\pager\PagerPluginBase;
use Drupal\views\ViewExecutable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Address search for sorting the results to show the nearest units first.
 *
 * Instead of altering the query, this unconventional way of sorting the results
 * is used because the information used for sorting is not stored in the
 * database as such.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("address_search")
 */
class AddressSearch extends FilterPluginBase {

  /**
   * Provide a simple textfield for street address.
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $form['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Street address'),
      '#size' => 30,
      '#default_value' => $this->value,
    ];

    $form_state->set('exposed', TRUE);
  }

  /**
   * Ignore the default filter query.
   */
  public function query() {
  }

  /**
   * Gets view results sorted by address search.
   *
   * This should be called from hook_views_pre_render() to replace the contents
   * of the $view->result.
   *
   * @param \Drupal\views\ViewExecutable $view
   *   The ViewExecutable from the hook's parameter.
   *
   * @return \Drupal\views\ViewExecutable
   *   Sorted results or original results when there is no address input.
   */
  public static function sortByAddress(ViewExecutable $view): ViewExecutable {
    $exposedInput = $view->getExposedInput();
    if (empty($exposedInput['address_search'])) {
      $view->result = AddressSearch::limitByPaging($view->result, $view->pager);
      return $view;
    }

    // Get the coordinates.
    $coordinates = AddressSearch::fetchAddressCoordinates($exposedInput['address_search']);
    if (empty($coordinates)) {
      $view->result = AddressSearch::limitByPaging($view->result, $view->pager);
      $view->element = AddressSearch::storeSearchStatus($view->element, FALSE);
      return $view;
    }

    // Calculate distances for each view result.
    $results = $view->result;
    $distances = [];
    foreach ($results as $result) {
      if (empty($result->_entity->get('latitude')) || empty($result->_entity->get('longitude'))) {
        continue;
      }
      $distances[$result->_entity->get('id')->getString()] = AddressSearch::calculateDistance(
        (float) $coordinates['lat'],
        (float) $coordinates['lon'],
        (float) $result->_entity->get('latitude')->getString(),
        (float) $result->_entity->get('longitude')->getString());

      // Set the distance to computed field.
      $result->_entity->set('distance', $distances[$result->_entity->get('id')->getString()]);
    }

    // Sort results array by distances: nearest first.
    uasort($results, function ($left, $right) use ($distances) {
      return match ($distances[$left->_entity->get('id')->getString()] >= $distances[$right->_entity->get('id')->getString()]) {
        FALSE => (-1),
        TRUE => 1,
      };
    });

    $results = AddressSearch::limitByPaging($results, $view->pager);

    $results = array_values($results);
    foreach ($results as $key => $row) {
      $row->index = $key;
    }
    $view->result = $results;

    $view->element = AddressSearch::storeSearchStatus($view->element, TRUE);
    return $view;
  }

  /**
   * Fetches the coordinates for given address using the ServiceMap API.
   *
   * @param string $address
   *   The street address.
   *
   * @return array
   *   Latitude and longitude coordinates in array, or empty array.
   */
  protected static function fetchAddressCoordinates(string $address): array {
    $client = new Client([
      'base_uri' => 'https://api.hel.fi/servicemap/v2/',
    ]);
    try {
      $response = $client->get('search', [
        'query' => [
          'q' => $address,
          'type' => 'address',
          'page' => '1',
          'page_size' => '1',
        ],
      ]);
    }
    catch (RequestException $e) {
      return [];
    }
    $addressSearchResult = Json::decode($response->getBody());

    if (empty($addressSearchResult["results"][0]["location"]["coordinates"][1]) ||
      empty($addressSearchResult["results"][0]["location"]["coordinates"][0])) {
      return [];
    }

    return [
      'lat' => $addressSearchResult["results"][0]["location"]["coordinates"][1],
      'lon' => $addressSearchResult["results"][0]["location"]["coordinates"][0],
    ];
  }

  /**
   * Calculates the distance between two coordinate points.
   *
   * @param float $latA
   *   Point A latitude.
   * @param float $lonA
   *   Point A longitude.
   * @param float $latB
   *   Point B latitude.
   * @param float $lonB
   *   Point B longitude.
   *
   * @return int
   *   Returns the distance in meters.
   */
  protected static function calculateDistance(float $latA, float $lonA, float $latB, float $lonB): int {
    $rad = M_PI / 180;
    $radius = 6371000;

    $latA = $latA * $rad;
    $lonA = $lonA * $rad;
    $latB = $latB * $rad;
    $lonB = $lonB * $rad;

    $latDelta = $latB - $latA;
    $lonDelta = $lonB - $lonA;

    // Calculate distance using the Haversine formula.
    return (int) round(2 * $radius * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latA) * cos($latB) * pow(sin($lonDelta / 2), 2))), 0);
  }

  /**
   * Slice results to smaller array using the pager settings.
   *
   * @param \Drupal\views\ResultRow[] $results
   *   Array of ResultRow.
   * @param \Drupal\views\Plugin\views\pager\PagerPluginBase $pager
   *   Pager.
   *
   * @return \Drupal\views\ResultRow[]
   *   Array subset.
   */
  protected static function limitByPaging(array $results, PagerPluginBase $pager): array {
    if (!is_int($pager->getItemsPerPage()) || $pager->getItemsPerPage() === 0) {
      return $results;
    }

    $itemsPerPage = $pager->getItemsPerPage();
    $offset = ($pager->getCurrentPage() * $itemsPerPage);
    return array_slice($results, $offset, $itemsPerPage, TRUE);
  }

  /**
   * Store the search status to element array.
   *
   * @param array $element
   *   ViewExecutable element array.
   * @param bool $succeed
   *   TRUE if the search was successful, FALSE if the address was not found.
   *
   * @return array
   *   ViewExecutable element array.
   */
  protected static function storeSearchStatus(array $element, bool $succeed): array {
    if (!isset($element['address_search_succeed'])) {
      $element['address_search_succeed'] = $succeed;
    }
    return $element;
  }

}
