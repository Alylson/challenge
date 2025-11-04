<?php

namespace Drupal\voting_system\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\voting_system\Service\VotingService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Provides a 'Voting Call' Block.
 *
 * @Block(
 *   id = "voting_call_block",
 *   admin_label = @Translation("Voting Call Block"),
 * )
 */
class VotingCallBlock extends BlockBase implements ContainerFactoryPluginInterface
{
    protected VotingService $votingService;

    public function __construct(
        array $configuration,
        $pluginId,
        $pluginDefinition,
        VotingService $votingService
    ) {
        parent::__construct($configuration, $pluginId, $pluginDefinition);
        $this->votingService = $votingService;
    }

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $pluginId,
        $pluginDefinition
    ) {
        return new static(
            $configuration,
            $pluginId,
            $pluginDefinition,
            $container->get('voting_system.voting_service')
        );
    }

    public function build()
    {
        $build = [];
        $questions = $this->votingService->getAllQuestions();

        if (empty($questions)) {
            $build['empty'] = [
                '#markup' => $this->t('Nenhuma pergunta disponível.'),
            ];
            return $build;
        }

        $items = [];
        foreach ($questions as $q) {
            $url = Url::fromRoute(
                'voting_system.api_question',
                [
                    'identifier' => $q['identifier']
                ]
            );
            $link = Link::fromTextAndUrl($q['title'], $url)->toString();
            $items[] = $link;
        }

        $build['questions'] = [
            '#theme' => 'item_list',
            '#items' => $items,
            '#title' => $this->t('Perguntas'),
        ];

        return $build;
    }
}
