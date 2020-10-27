<?php

namespace Drupal\helfi_trp\Entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the helfi_trp entity edit forms.
 */
class UnitForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $result = $entity->save();
    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => render($link)];

    if ($result == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('New helfi_trp %label has been created.', $message_arguments));
      $this->logger('helfi_trp')->notice('Created new helfi_trp %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The helfi_trp %label has been updated.', $message_arguments));
      $this->logger('helfi_trp')->notice('Updated new helfi_trp %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.trp_unit.canonical', ['trp_unit' => $entity->id()]);
  }

}
