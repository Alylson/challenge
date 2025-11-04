<?php

namespace Drupal\voting_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\voting_system\Service\VotingService;

class VotingQuestionController extends ControllerBase
{
    protected $votingService;

    public function __construct(VotingService $votingService)
    {
        $this->votingService = $votingService;
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('voting_system.voting_service')
        );
    }

    public function content($identifier)
    {
        $question = $this->votingService->getQuestionByIdentifier($identifier);

        if (!$question) {
            return [
                '#markup' => '<p>Pergunta não encontrada.</p>',
            ];
        }

        $options = $question['options'] ?? [];

        if (empty($options)) {
            return [
                '#markup' => '<p>Não há opções disponíveis para esta pergunta.</p>',
            ];
        }

        $displayOptions = $options;
        if (!$question['show_results']) {
            $displayOptions = [];
            foreach ($options as $option) {
                $displayOptions[] = [
                    'id' => $option['id'],
                    'title' => $option['title'],
                    'description' => $option['description'],
                    'image' => $option['image'],
                ];
            }
        }

        $build = [
            '#theme' => 'voting_question_page',
            '#question' => [
                'title' => $question['title'],
                'id' => $question['id'],
                'identifier' => $identifier,
                'show_results' => $question['show_results'],
            ],
            '#options' => $displayOptions,
        ];

        $build['voting_form'] = \Drupal::formBuilder()->getForm(
            'Drupal\voting_system\Form\VotingForm',
            $question['id'],
            $displayOptions,
            $question['show_results'],
            $question['title']
        );

        return $build;
    }

    public function getTitle($identifier)
    {
        $question = $this->votingService->getQuestionByIdentifier($identifier);

        return $question ? $question['title'] : 'Voting';
    }
}
