<?php

namespace Drupal\voting_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\voting_system\Service\VotingService;

class VotingPageController extends ControllerBase
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

    public function content()
    {
        $questions = $this->votingService->getAllQuestions();

        if (empty($questions)) {
            $markup = '<p>Nenhuma pergunta disponível.</p>';
        } else {
            $markup = '<div class="voting-questions">';

            foreach ($questions as $question) {
                $options = $this->votingService->getQuestionOptions($question['id']);

                $markup .= '<div class="voting-question">';
                $markup .= '<h3>' . $question['title'] . '</h3>';

                if (!empty($options)) {
                    $markup .= '<ul class="voting-options">';
                    foreach ($options as $option) {
                        if ($question['show_results']) {
                            $markup .= '<li>' . $option['title'] . ' (' . $option['votes_count'] . ' votes)</li>';
                        } else {
                            $markup .= '<li>' . $option['title'] . '</li>';
                        }
                    }
                    $markup .= '</ul>';
                } else {
                    $markup .= '<p>Nenhuma opção disponível para essa pergunta.</p>';
                }

                $markup .= '<p><a href="/voting/' . $question['identifier'] . '" class="button">Vote aqui</a></p>';
                $markup .= '</div>';
            }

            $markup .= '</div>';
        }

        return [
            '#markup' => $markup,
        ];
    }
}
