<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Service\Stats\PlayerGameStatsProvider;

/**
 * Class PlayerGameStatsController
 */
class PlayerGameStatsController extends AbstractController
{
    private PlayerGameStatsProvider $playerGameStatsProvider;

    public function __construct(PlayerGameStatsProvider $playerGameStatsProvider)
    {
        $this->playerGameStatsProvider = $playerGameStatsProvider;
    }

    /**
     * @param Player    $player
     * @return array
     */
    public function load(Player $player): array
    {
        return $this->playerGameStatsProvider->load($player);
    }
}
