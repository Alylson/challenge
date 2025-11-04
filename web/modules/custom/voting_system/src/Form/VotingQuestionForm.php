<?php

namespace Drupal\voting_system\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class VotingQuestionForm extends EntityForm
{
    public function form(array $form, FormStateInterface $formState)
    {
        $votingQuestion = $this->entity;

        $form['title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Título'),
            '#default_value' => $votingQuestion->get('title'),
            '#required' => TRUE,
        ];

        $form['id'] = [
            '#type' => 'machine_name',
            '#title' => $this->t('Nome para identificação'),
            '#default_value' => $votingQuestion->id(),
            '#machine_name' => [
                'exists' => ['\Drupal\voting_system\Entity\VotingQuestion', 'load'],
            ],
            '#disabled' => !$votingQuestion->isNew(),
        ];

        $form['identifier'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Identificador Único'),
            '#default_value' => $votingQuestion->get('identifier'),
            '#description' => $this->t('Identificador único para acesso à API externa.'),
            '#required' => TRUE,
        ];

        $form['enabled'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Habilitar pergunta'),
            '#default_value' => $votingQuestion->get('enabled'),
        ];

        $form['show_results'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Mostrar resultados após votar'),
            '#default_value' => $votingQuestion->get('show_results'),
        ];

        if (!$votingQuestion->isNew()) {
            $form['options_section'] = [
                '#type' => 'details',
                '#title' => $this->t('Opções de Votação'),
                '#open' => TRUE,
            ];

            $options_count = $votingQuestion->getOptionCount();

            $form['options_section']['options_count'] = [
                '#markup' => $this->t('<p>Esta pergunta tem <strong>@count</strong> opções.</p>', ['@count' => $options_count]),
            ];

            $form['options_section']['manage_options'] = [
                '#type' => 'link',
                '#title' => $this->t('Gerenciar Opções de Votação'),
                '#url' => Url::fromRoute('entity.voting_option.collection', [], [
                    'query' => ['question_id' => $votingQuestion->id()]
                ]),
                '#attributes' => [
                    'class' => ['button', 'button--primary'],
                ],
            ];

            $form['options_section']['add_option'] = [
                '#type' => 'link',
                '#title' => $this->t('Adicionar opção de votação'),
                '#url' => Url::fromRoute('entity.voting_option.add_form', [], [
                    'query' => ['question_id' => $votingQuestion->id()]
                ]),
                '#attributes' => [
                    'class' => ['button'],
                ],
            ];
        }

        return parent::form($form, $formState);
    }

    public function save(array $form, FormStateInterface $formState)
    {
        $votingQuestion = $this->entity;

        if ($votingQuestion->isNew() && !$votingQuestion->id()) {
            $votingQuestion->set('id', $formState->getValue('id'));
        }

        $status = $votingQuestion->save();

        if ($status == SAVED_NEW) {
            $this->messenger()->addMessage($this->t('Criada nova pergunta "%label".', ['%label' => $votingQuestion->label()]));
            $this->messenger()->addMessage($this->t('Agora você já pode criar opções de votação.'));
        } else {
            $this->messenger()->addMessage($this->t('Pergunta "%label" atualizada com sucesso.', ['%label' => $votingQuestion->label()]));
        }

        $formState->setRedirect('entity.voting_question.collection');
    }
}
