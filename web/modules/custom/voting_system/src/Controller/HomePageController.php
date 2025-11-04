<?php

namespace Drupal\voting_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Block\BlockManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HomePageController extends ControllerBase
{
    protected $blockManager;

    public function __construct(BlockManagerInterface $blockManager)
    {
        $this->blockManager = $blockManager;
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('plugin.manager.block')
        );
    }

    public function content()
    {
        $block = $this->blockManager->createInstance('voting_homepage_block', []);

        $render = $block->build();

        return [
            '#theme' => 'container',
            '#attributes' => ['class' => ['homepage-wrapper']],
            'content' => $render,
        ];
    }
}
