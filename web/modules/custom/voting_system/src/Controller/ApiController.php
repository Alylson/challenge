<?php

namespace Drupal\voting_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\voting_system\Service\VotingService;

class ApiController extends ControllerBase
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

    public function listQuestions()
    {
        $questions = $this->votingService->getAllQuestions();

        $response = [
            'status' => 'success',
            'count' => count($questions),
            'data' => $questions,
            'timestamp' => time(),
        ];

        return new JsonResponse($response);
    }

    public function getQuestion($identifier)
    {
        $question = $this->votingService->getQuestionByIdentifier($identifier);

        if (!$question) {
            $response = [
                'status' => 'error',
                'message' => 'Question not found',
                'identifier' => $identifier,
            ];
            return new JsonResponse($response, 404);
        }

        $response = [
            'status' => 'success',
            'data' => $question,
        ];

        return new JsonResponse($response);
    }

    public function vote(Request $request, $identifier)
    {
        if ($request->getContentType() !== 'json') {
            $response = [
                'status' => 'error',
                'message' => 'Content-Type must be application/json',
            ];
            return new JsonResponse($response, 400);
        }

        $data = json_decode($request->getContent(), TRUE);

        if (empty($data['option_id'])) {
            $response = [
                'status' => 'error',
                'message' => 'Missing required parameter: option_id',
            ];
            return new JsonResponse($response, 400);
        }

        try {
            $result = $this->votingService->recordVote(
                $identifier, (int) $data['option_id']
            );

            $response = [
                'status' => 'success',
                'message' => $result,
                'vote' => [
                    'question_identifier' => $identifier,
                    'option_id' => $data['option_id'],
                    'timestamp' => time(),
                ],
            ];

            return new JsonResponse($response);
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return new JsonResponse($response, 500);
        }
    }

    public function results($identifier)
    {
        $results = $this->votingService->getResults($identifier);

        if (isset($results['error'])) {
            $response = [
                'status' => 'error',
                'message' => $results['error'],
            ];
            return new JsonResponse($response, 404);
        }

        $response = [
            'status' => 'success',
            'data' => $results,
        ];

        return new JsonResponse($response);
    }
}
