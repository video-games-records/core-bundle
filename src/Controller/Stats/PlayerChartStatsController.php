<?php

namespace VideoGamesRecords\CoreBundle\Controller\Stats;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Repository\PlayerChartStatusRepository;

/**
 * Class PlayerController
 */
class PlayerChartStatsController extends AbstractController
{
    private PlayerChartStatusRepository $playerChartStatusRepository;

    public function __construct(PlayerChartStatusRepository $playerChartStatusRepository)
    {
        $this->playerChartStatusRepository = $playerChartStatusRepository;
    }

    /**
     * @param Player    $player
     * @return mixed
     */
    public function load(Player $player): mixed
    {
        return $this->playerChartStatusRepository->getStatsFromPlayer($player);
    }
}
