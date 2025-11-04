<?php

namespace Drupal\voting_system\Form;

use Drupal\Core\Entity\EntityDeleteForm;

class VotingQuestionDeleteForm extends EntityDeleteForm
{
    public function getQuestion()
    {
        return $this->t('Tem certeza que deseja excluir a pergunta %label?', [
            '%label' => $this->entity->label(),
        ]);
    }

    public function getConfirmText()
    {
        return $this->t('Excluir');
    }

    public function getCancelUrl()
    {
        return $this->entity->toUrl('collection');
    }

    public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state)
    {
        $this->entity->delete();
        $this->messenger()->addMessage($this->t('Pergunta %label excluída com sucesso.', [
            '%label' => $this->entity->label(),
        ]));

        $form_state->setRedirectUrl($this->getCancelUrl());
    }
}
