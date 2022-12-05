<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

use Drupal\views\Views;

/**
 * Tests the ServiceIdArgument views argument handler.
 *
 * @group helfi_tpr
 */
class ServiceIdArgumentTest extends MigrationTestBase {

  /**
   * Tests the service id argument.
   */
  public function testServiceIdArgument() : void {
    // Service migration has a soft dependency on unit migration.
    $this->runUnitMigrate();
    $this->runServiceMigrate();

    // Testing fixtures data is received.
    $view = Views::getView('test_service_list');
    $view->setDisplay();
    $view->initHandlers();
    $view->preExecute();
    $view->execute();
    // Expecting 4 results from fixtures data.
    $this->assertEquals(4, count($view->result));
    $view->destroy();

    // Test for service_id.
    $view = Views::getView('test_service_list');
    $view->setDisplay();
    $view->initHandlers();
    // Filtering by service_id = 10554.
    $view->setArguments(['id_or_service_id' => '|10554']);
    $view->preExecute();
    $view->execute();

    // Expecting 3 results: id's: 1,2,3 that are under service_id = 10554.
    $this->assertEquals(3, count($view->result));
    foreach ($view->result as &$row) {
      $this->assertEquals('10554', $view->field['service_id']->getValue($row));
    }
    $view->destroy();

    // Test for id.
    $view = Views::getView('test_service_list');
    $view->setDisplay();
    $view->initHandlers();
    // Filtering by id = 1.
    $view->setArguments(['id_or_service_id' => '1|']);
    $view->preExecute();
    $view->execute();

    // Expecting 1 result: id = 1.
    $this->assertEquals(1, count($view->result));
    foreach ($view->result as &$row) {
      $this->assertEquals('1', $view->field['id']->getValue($row));
    }
    $view->destroy();

    // Test for service_id and id.
    $view = Views::getView('test_service_list');
    $view->setDisplay();
    $view->initHandlers();
    $view->setArguments(['id_or_service_id' => '7705|10554']);
    $view->preExecute();
    $view->execute();

    // Expecting all id's under 10554 service_id and one specific id = 7705.
    $this->assertEquals(4, count($view->result));
    $this->assertEquals(10554, $view->field['service_id']->getValue($view->result[0]));
    $this->assertEquals(10554, $view->field['service_id']->getValue($view->result[1]));
    $this->assertEquals(10554, $view->field['service_id']->getValue($view->result[2]));
    $this->assertEquals(7705, $view->field['id']->getValue($view->result[3]));

    $view->destroy();

    // Test for two id's.
    $view = Views::getView('test_service_list');
    $view->setDisplay();
    $view->initHandlers();
    $view->setArguments(['id_or_service_id' => '1,2|']);
    $view->preExecute();
    $view->execute();

    $this->assertEquals(2, count($view->result));
    $this->assertEquals(1, $view->field['id']->getValue($view->result[0]));
    $this->assertEquals(2, $view->field['id']->getValue($view->result[1]));

    $view->destroy();

    // Test for two service_id's.
    $view = Views::getView('test_service_list');
    $view->setDisplay();
    $view->initHandlers();
    $view->setArguments(['id_or_service_id' => '|10554,10014']);
    $view->preExecute();
    $view->execute();

    $this->assertEquals(4, count($view->result));
    $this->assertEquals(10554, $view->field['service_id']->getValue($view->result[0]));
    $this->assertEquals(10554, $view->field['service_id']->getValue($view->result[1]));
    $this->assertEquals(10554, $view->field['service_id']->getValue($view->result[2]));
    $this->assertEquals(10014, $view->field['service_id']->getValue($view->result[3]));

    $view->destroy();

    // Testing no result.
    $view = Views::getView('test_service_list');
    $view->setDisplay();
    $view->initHandlers();
    // Filtering by service_id|id.
    $view->setArguments(['id_or_service_id' => '10554|1']);
    $view->preExecute();
    $view->execute();
    // Expecting 0 results.
    $this->assertEquals(0, count($view->result));
    $view->destroy();
  }

}
