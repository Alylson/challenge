<?php

namespace Drupal\voting_system\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Voting Block.
 *
 * @Block(
 *   id = "voting_system_block",
 *   admin_label = @Translation("Voting System Block")
 * )
 */
class VotingBlock extends BlockBase
{
    protected FormBuilderInterface $formBuilder;

    public function __construct(
        array $configuration,
        $pluginId,
        $pluginDefinition,
        FormBuilderInterface $formBuilder
    ) {
        parent::__construct($configuration, $pluginId, $pluginDefinition);
        $this->formBuilder = $formBuilder;
    }

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $pluginId,
        $pluginDefinition
    ) {
        return new static($configuration, $pluginId, $pluginDefinition, $container->get('form_builder'));
    }

    public function build()
    {
        return [
            '#markup' => $this->t('Voting System Block'),
            'form' => $this->formBuilder->getForm('\Drupal\voting_system\Form\VotingForm'),
        ];
    }
}
