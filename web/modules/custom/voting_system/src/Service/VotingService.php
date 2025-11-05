<?php

namespace Drupal\voting_system\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;

class VotingService
{
    protected $entityTypeManager;

    public function __construct(EntityTypeManagerInterface $entityTypeManager)
    {
        $this->entityTypeManager = $entityTypeManager;
    }

    public function getAllQuestions()
    {
        try {
            $questions = $this->entityTypeManager
                ->getStorage('voting_question')
                ->loadByProperties(['enabled' => TRUE]);

            $result = [];
            foreach ($questions as $question) {
                $result[] = [
                    'id' => $question->id(),
                    'title' => $question->label(),
                    'identifier' => $question->get('identifier'),
                    'enabled' => $question->isEnabled(),
                    'show_results' => $question->get('show_results'),
                    'options' => $this->getQuestionOptions($question->id()),
                ];
            }

            return $result;
        } catch (\Exception $e) {
            \Drupal::logger('voting_system')->error('Erro ao carregar perguntas: @error', ['@error' => $e->getMessage()]);
            return [];
        }
    }

    public function getQuestionOptions($questionId)
    {
        try {
            $options = $this->entityTypeManager
                ->getStorage('voting_option')
                ->loadByProperties(['question_id' => $questionId]);

            $result = [];
            foreach ($options as $option) {
                $result[] = [
                    'id' => $option->id(),
                    'title' => $option->label(),
                    'description' => $option->get('description'),
                    'image' => $option->get('image'),
                    'votes_count' => $option->get('votes_count') ?: 0,
                ];
            }

            return $result;
        } catch (\Exception $e) {
            \Drupal::logger('voting_system')->error('Erro ao carregar opções para pergunta @id: @error', [
                '@id' => $questionId,
                '@error' => $e->getMessage()
            ]);
            return [];
        }
    }

    public function getQuestionByIdentifier($identifier)
    {
        try {
            $questions = $this->entityTypeManager
                ->getStorage('voting_question')
                ->loadByProperties(['identifier' => $identifier]);

            if (empty($questions)) {
                return null;
            }

            $question = reset($questions);

            return [
                'id' => $question->id(),
                'title' => $question->label(),
                'identifier' => $question->get('identifier'),
                'enabled' => $question->isEnabled(),
                'show_results' => $question->get('show_results'),
                'options' => $this->getQuestionOptions($question->id()),
            ];
        } catch (\Exception $e) {
            \Drupal::logger('voting_system')->error('Erro ao carregar pergunta pelo identificador @identifier: @error', [
                '@identifier' => $identifier,
                '@error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function recordVote($questionIdentifier, $optionId, $userId = NULL)
    {
        if (!$userId) {
            $userId = \Drupal::currentUser()->id();
        }

        $question = $this->getQuestionByIdentifier($questionIdentifier);
        if (!$question) {
            throw new \Exception('Pergunta não encontrada');
        }

        $questionId = $question['id'];

        if ($this->hasUserVoted($questionId, $userId)) {
            throw new \Exception('Você já votou nesta pergunta');
        }

        $option = $this->entityTypeManager->getStorage('voting_option')->load($optionId);
        if (!$option) {
            throw new \Exception('Opção não encontrada');
        }

        $optionQuestionId = $option->get('question_id');
        if ($optionQuestionId !== $questionId) {
            throw new \Exception('A opção não pertence a esta pergunta');
        }

        try {
            $currentVotes = $option->get('votes_count') ?: 0;
            $option->set('votes_count', $currentVotes + 1);
            $option->save();

            $this->recordUserVote($questionId, $optionId, $userId);

            return 'Voto registrado com sucesso';
        } catch (\Exception $e) {
            \Drupal::logger('voting_system')->error(
                'Erro ao registrar voto: @error',
                [
                    '@error' => $e->getMessage()
                ]
            );
            throw $e;
        }
    }

    public function getResults($identifier)
    {
        try {
            $question = $this->getQuestionByIdentifier($identifier);

            if (!$question) {
                return ['error' => 'Pergunta não encontrada'];
            }

            if (!$question['show_results']) {
                return ['error' => 'Resultados não disponíveis para essa pergunta'];
            }

            $totalVotes = 0;
            foreach ($question['options'] as $option) {
                $totalVotes += $option['votes_count'];
            }

            $results = [
                'question' => [
                    'id' => $question['id'],
                    'title' => $question['title'],
                    'identifier' => $question['identifier'],
                    'total_votes' => $totalVotes,
                ],
                'options' => [],
            ];

            foreach ($question['options'] as $option) {
                $percentage = $totalVotes > 0 ? round(($option['votes_count'] / $totalVotes) * 100, 2) : 0;

                $results['options'][] = [
                    'id' => $option['id'],
                    'title' => $option['title'],
                    'description' => $option['description'],
                    'votes_count' => $option['votes_count'],
                    'percentage' => $percentage,
                ];
            }

            return $results;
        } catch (\Exception $e) {
            \Drupal::logger('voting_system')->error('Erro ao obter resultados: @error', [
                '@error' => $e->getMessage()
            ]);
            return ['error' => 'Falha ao obter resultados'];
        }
    }

    public function hasUserVoted($questionId, $userId = NULL)
    {
        if (!$userId) {
            $userId = \Drupal::currentUser()->id();
        }

        if ($userId == 0) {
            return $this->hasAnonymousVoted($questionId);
        }

        try {
            $votes = $this->entityTypeManager
            ->getStorage('voting_tracking')
            ->loadByProperties([
                'user_id' => $userId,
                'question_id' => $questionId,
            ]);

            return !empty($votes);
        } catch (\Exception $e) {
            \Drupal::logger('voting_system')->error('Erro ao verificar voto do usuário: @error', [
            '@error' => $e->getMessage()
            ]);
            return FALSE;
        }
    }

    protected function hasAnonymousVoted($questionId)
    {
        $session = \Drupal::request()->getSession();
        $votedQuestions = $session->get('voting_system_voted_questions', []);

        return in_array($questionId, $votedQuestions);
    }

    protected function recordUserVote($questionId, $optionId, $userId)
    {
        if ($userId != 0) {
            $vote = $this->entityTypeManager->getStorage('voting_tracking')->create([
                'user_id' => $userId,
                'question_id' => $questionId,
                'option_id' => $optionId,
            ]);
            $vote->save();
        } else {
            $session = \Drupal::request()->getSession();
            $voted_questions = $session->get('voting_system_voted_questions', []);
            $voted_questions[] = $questionId;
            $session->set('voting_system_voted_questions', $voted_questions);
        }
    }

    public function getUserVote($questionId, $userId = NULL)
    {
        if (!$userId) {
            $userId = \Drupal::currentUser()->id();
        }

        if ($userId == 0) {
            return $this->hasAnonymousVoted($questionId) ? 'voted' : NULL;
        }

        try {
            $votes = $this->entityTypeManager
                ->getStorage('voting_tracking')
                ->loadByProperties(
                    [
                        'user_id' => $userId,
                        'question_id' => $questionId,
                    ]
                );

            if (!empty($votes)) {
                $vote = reset($votes);
                return $vote->get('option_id')->value;
            }

            return NULL;
        } catch (\Exception $e) {
            \Drupal::logger('voting_system')->error('Erro ao obter voto do usuário: @error', [
            '@error' => $e->getMessage()
            ]);
            return NULL;
        }
    }
}
