<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\views\relationship;

use Drupal\views\Plugin\views\relationship\RelationshipPluginBase;

/**
 * Tags queries for ontology word details relationship.
 *
 * @ingroup views_relationship_handlers
 *
 * @ViewsRelationship("tag_owd_relationship")
 */
class TagOwdRelationship extends RelationshipPluginBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    parent::query();
    $this->query->addTag('owd_relationship');
  }

}
