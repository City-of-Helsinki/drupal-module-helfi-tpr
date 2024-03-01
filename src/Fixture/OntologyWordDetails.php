<?php

declare(strict_types=1);

namespace Drupal\helfi_tpr\Fixture;

use Drupal\helfi_api_base\Fixture\FixtureBase;
use GuzzleHttp\Psr7\Response;

/**
 * Provides fixture data for tpr_ontology_word_details migration.
 */
final class OntologyWordDetails extends FixtureBase {

  /**
   * Gets the mock data.
   *
   * @return array[]
   *   The mock data.
   */
  public function getMockData() : array {
    return [
      [
        'id' => 157,
        'ontologyword_fi' => 'erityistehtävän mukaiset lukiot',
        'ontologyword_sv' => 'gymnasier enligt specialuppgift',
        'ontologyword_en' => 'special educational mission upper secondary schools',
        'can_add_schoolyear' => TRUE,
        'can_add_clarification' => TRUE,
        'unit_ids' => [
          6820,
          7051,
          15348,
          18649,
          19274,
          19428,
          19890,
          30854,
          30855,
          30858,
          30862,
          30863,
          30864,
          61450,
        ],
        'details' => [
          [
            'unit_id' => 30855,
            'ontologyword_id' => 157,
            'schoolyear' => '2021-2022',
            'clarification_fi' => 'kuvataide',
            'clarification_sv' => 'bildkonst',
            'clarification_en' => 'visual arts',
          ],
          [
            'unit_id' => 30862,
            'ontologyword_id' => 157,
            'schoolyear' => '2021-2022',
            'clarification_fi' => 'urheilu',
            'clarification_sv' => 'idrott',
            'clarification_en' => 'sports',
          ],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getMockResponses() : array {
    $owds = $this->getMockData();
    $responses = [
      new Response(200, [], json_encode($owds)),
    ];

    foreach ($owds as $owd) {
      $responses[] = new Response(200, [], json_encode($owd['details']));
    }
    return $responses;
  }

}
