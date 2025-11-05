<?php

namespace Drupal\voting_system\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block for the voting homepage.
 *
 * @Block(
 *   id = "voting_homepage_block",
 *   admin_label = @Translation("Voting Homepage Block")
 * )
 */
class VotingHomePageBlock extends BlockBase
{
    public function build()
    {
        return [
            '#markup' => '<div class="voting-homepage"><a href="/voting">Ir para a página de votação</a></div>',
        ];
    }
}
