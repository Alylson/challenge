<?php

namespace Drupal\voting_system\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

class VotingOptionForm extends EntityForm
{
    public function form(array $form, FormStateInterface $formState)
    {
        $votingOption = $this->entity;

        $form['title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Título'),
            '#default_value' => $votingOption->get('title'),
            '#required' => TRUE,
        ];

        $form['id'] = [
            '#type' => 'machine_name',
            '#title' => $this->t('Nome para identificação'),
            '#default_value' => $votingOption->id(),
            '#machine_name' => [
                'exists' => ['\Drupal\voting_system\Entity\VotingOption', 'load'],
            ],
            '#disabled' => !$votingOption->isNew(),
        ];

        $form['description'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Descrição'),
            '#default_value' => $votingOption->get('description'),
            '#description' => $this->t('A descrição é opcional'),
        ];

        $form['image'] = [
            '#type' => 'textfield',
            '#title' => $this->t('URL da imagem'),
            '#default_value' => $votingOption->get('image'),
            '#description' => $this->t('URL da imagem'),
        ];

        $form['votes_count'] = [
            '#type' => 'number',
            '#title' => $this->t('Total de votos'),
            '#default_value' => $votingOption->get('votes_count') ?: 0,
            '#min' => 0,
        ];

        $questionId = \Drupal::request()->query->get('question_id');
        if ($votingOption->isNew() && $questionId) {
            $votingOption->set('question_id', $questionId);
        }

        $form['question_id'] = [
            '#type' => 'select',
            '#title' => $this->t('Pergunta para votação'),
            '#default_value' => $votingOption->get('question_id'),
            '#options' => $this->getQuestionOptions(),
            '#required' => TRUE,
            '#description' => $this->t('Selecione a pergunta de votação'),
        ];

        return parent::form($form, $formState);
    }

    protected function getQuestionOptions()
    {
        $questions = \Drupal::entityTypeManager()
            ->getStorage('voting_question')
            ->loadMultiple();

        $options = [];
        foreach ($questions as $question) {
            $options[$question->id()] = $question->label();
        }

        return $options;
    }

    public function save(array $form, FormStateInterface $formState)
    {
        $votingOption = $this->entity;

        if ($votingOption->isNew() && !$votingOption->id()) {
            $votingOption->set('id', $formState->getValue('id'));
        }

        $status = $votingOption->save();

        if ($status == SAVED_NEW) {
            $this->messenger()->addMessage(
                $this->t('Opção "%label" foi criado com sucesso.',
                [
                    '%label' => $votingOption->label()
                ]
                )
            );
        } else {
            $this->messenger()->addMessage($this->t('Opção "%label" foi atualizado com sucesso.', ['%label' => $votingOption->label()]));
        }

        $formState->setRedirect('entity.voting_option.collection');
    }
}
