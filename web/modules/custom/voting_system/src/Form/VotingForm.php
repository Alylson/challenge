<?php

namespace Drupal\voting_system\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class VotingForm extends FormBase
{
    protected $questionId;
    protected $options;
    protected $showResults;
    protected $questionTitle;
    protected $fileUrlGenerator;

    public function getFormId()
    {
        return 'voting_system_voting_form';
    }

    public function __construct(FileUrlGeneratorInterface $fileUrlGenerator)
    {
        $this->fileUrlGenerator = $fileUrlGenerator;
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('file_url_generator')
        );
    }

    public function buildForm(
        array $form,
        FormStateInterface $formState,
        $questionId = NULL,
        $options = [],
        $showResults = TRUE,
        $questionTitle = ''
    ) {
        $this->questionId = $questionId;
        $this->options = $options;
        $this->showResults = $showResults;
        $this->questionTitle = $questionTitle;

        $voting_service = \Drupal::service('voting_system.voting_service');
        $hasVoted = $voting_service->hasUserVoted($questionId);
        $userVote = $voting_service->getUserVote($questionId);

        $form['question_id'] = [
            '#type' => 'hidden',
            '#value' => $questionId,
        ];

        $form['title'] = [
            '#markup' =>
            '<h3>' . $this->t(
                '@question',
                [
                    '@question' => $this->questionTitle
                ]
            ) . '</h3>',
        ];

        if ($hasVoted) {
            $form['already_voted'] = [
                '#markup' => '<div class="already-voted-message"><p>' . 
                    $this->t('Você já votou nesta pergunta. Obrigado pela sua participação!') . 
                    '</p></div>',
            ];

            if ($userVote && $userVote !== 'voted') {
                $userOption = $this->getOptionTitle($userVote);
                if ($userOption) {
                    $form['user_vote'] = [
                        '#markup' => '<div class="user-vote-info"><p><strong>' . 
                            $this->t('Seu voto: @option', ['@option' => $userOption]) . 
                            '</strong></p></div>',
                    ];
                }
            }

            $form['results'] = [
                '#markup' => $this->buildResultsDisplay($options, $showResults),
            ];

            return $form;
        }

        $optionChoices = [];
        foreach ($options as $option) {
            $label = '';

            if (!empty($option['image'])) {
                $imageUrl = $this->fileUrlGenerator->generateAbsoluteString(
                    $option['image']
                );
                $label .= '<div class="option-with-image">';
                $label .= '<img src="' . $imageUrl . '" alt="' .
                    $option['title'] .
                    '" style="max-width: 50px;
                        max-height: 50px;
                        vertical-align: middle;
                        margin-right: 10px;">';
                $label .= '<span class="option-text">';
            }

            $label .= $option['title'];

            if ($this->showResults && isset($option['votes_count'])) {
                $label .= ' (' . $option['votes_count'] . ' votes)';
            }

            if (!empty($option['image'])) {
                $label .= '</span></div>';
            }

            $optionChoices[$option['id']] = $label;
        }

        $form['selected_option'] = [
            '#type' => 'radios',
            '#title' => $this->t('Escolha uma opção'),
            '#options' => $optionChoices,
            '#required' => TRUE,
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Votar'),
        ];

        $form['#attached']['library'][] = 'voting_system/voting-form';

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $formState)
    {
        $selectedOptionId = $formState->getValue('selected_option');
        $questionId = $formState->getValue('question_id');

        try {
            $question = \Drupal::entityTypeManager()->getStorage('voting_question')->load($questionId);
            if (!$question) {
                throw new \Exception('Pergunta não encontrada');
            }

            $questionIdentifier = $question->get('identifier');
            $votingService = \Drupal::service('voting_system.voting_service');
            $result = $votingService->recordVote($questionIdentifier, $selectedOptionId);

            $option = \Drupal::entityTypeManager()->getStorage('voting_option')
                ->load($formState->getValue('selected_option'));

            if ($option) {
                // $currentVotes = $option->get('votes_count') ?: 0;
                // $option->set('votes_count', $currentVotes + 1);
                // $option->save();

                $this->messenger()->addMessage($this->t('Obrigado! Seu voto para "%option" foi registrado com sucesso.', [
                    '%option' => $option->label()
                ]));
            }
        } catch (\Exception $e) {
            $this->messenger()->addError($this->t('Erro: @error', ['@error' => $e->getMessage()]));
        }

        $formState->setRedirect('voting_system.voting_page');
    }

    protected function getOptionTitle($optionId)
    {
        try {
            $option = \Drupal::entityTypeManager()->getStorage('voting_option')->load($optionId);
            return $option ? $option->label() : NULL;
        } catch (\Exception $e) {
            return NULL;
        }
    }

    protected function buildResultsDisplay($options, $showResults)
    {
        if (!$showResults) {
            return '<p>' . $this->t('Resultados não estão disponíveis para esta pergunta.') . '</p>';
        }

        $output = '<div class="voting-results"><h4>' . $this->t('Resultados Atuais:') . '</h4><ul>';
        
        foreach ($options as $option) {
            $output .= '<li>' . $option['title'] . ' (' . $option['votes_count'] . ' votos)</li>';
        }
        
        $output .= '</ul></div>';

        return $output;
    }
}
