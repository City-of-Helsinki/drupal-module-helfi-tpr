<?php

namespace Drupal\helfi_tpr\Plugin\views\filter;

use Drupal\helfi_tpr\Entity\OntologyWordDetails;

/**
 * Filter high school units by educational mission.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("educational_mission_filter")
 */
class EducationalMission extends OntologyWordDetailsBase {

  /**
   * {@inheritdoc}
   */
  public function generateOptions(): array {
    // @todo Get the current schoolyear e.g. from State API after the year
    // selection is implemented.
    $schoolyear = '2021-2022';

    $details = [];
    $multipleOntologyWordDetails = OntologyWordDetails::loadMultipleByWordId(157);
    foreach ($multipleOntologyWordDetails as $ontologyWordDetails) {
      /** @var \Drupal\helfi_tpr\Entity\OntologyWordDetails $ontologyWordDetails */
      $details[] = $ontologyWordDetails->getDetailByAnother('school_details', 'clarification', 'schoolyear', $schoolyear);
    }

    $options = array_merge(...$details);
    asort($options);
    return $options;
  }

}
