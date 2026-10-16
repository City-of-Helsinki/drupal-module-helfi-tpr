<?php

declare(strict_types=1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\helfi_address_search\Plugin\views\filter\AddressSearch;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\helfi_api_base\Traits\ApiTestTrait;
use Drupal\views\Plugin\views\pager\PagerPluginBase;
use Drupal\views\ViewExecutable;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests the address search.
 */
#[Group('helfi_tpr')]
#[RunTestsInSeparateProcesses]
class AddressSearchTest extends KernelTestBase {

  use ApiTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'views',
    'helfi_address_search',
  ];

  /**
   * Sorts an empty view by address.
   *
   * @param array<\Psr\Http\Message\ResponseInterface|\GuzzleHttp\Exception\GuzzleException> $responses
   *   The responses.
   * @param array<mixed> $history
   *   The HTTP history.
   *
   * @return bool
   *   Whether the address search succeeded.
   */
  private function search(array $responses, array &$history = []) : bool {
    $this->container->set('http_client', $this->createMockHistoryMiddlewareHttpClient($history, $responses));
    $plugin = $this->container->get('plugin.manager.views.filter')
      ->createInstance('address_search');
    $this->assertInstanceOf(AddressSearch::class, $plugin);

    $view = $this->createMock(ViewExecutable::class);
    $view->method('getExposedInput')
      ->willReturn(['address_search' => 'Mannerheimintie 1']);
    $view->pager = $this->createMock(PagerPluginBase::class);
    $view->result = [];
    $view->element = [];

    return $plugin->sortByAddress($view)->element['#address_search_succeed'];
  }

  /**
   * Tests a successful address search.
   */
  public function testSearch() : void {
    $body = json_encode([
      'results' => [
        ['location' => ['coordinates' => [24.94, 60.17]]],
      ],
    ], JSON_THROW_ON_ERROR);
    $history = [];

    $this->assertTrue($this->search([
      new Response(body: $body),
      new Response(body: $body),
    ], $history));
    $this->assertCount(2, $history);

    foreach ($history as $item) {
      $this->assertSame(AddressSearch::TIMEOUT, $item['options']['timeout']);
      $this->assertStringStartsWith(AddressSearch::BASE_URL . 'search', (string) $item['request']->getUri());
    }
  }

  /**
   * Make sure the failed requests don't throw an exception.
   */
  public function testFailedRequest() : void {
    $this->assertFalse($this->search([
      new Response(500),
      new Response(500),
    ]));
    $this->assertFalse($this->search([
      new ConnectException('Timeout', new Request('GET', AddressSearch::BASE_URL)),
      new ConnectException('Timeout', new Request('GET', AddressSearch::BASE_URL)),
    ]));
  }

}
