<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Repository\PlayerChartStatusRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;

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
    public function playerChartStatus(Player $player): mixed
    {
        return $this->playerChartStatusRepository->getStatsFromPlayer($player);
    }
}
