<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;

/**
 * Class PlayerController
 */
class PlayerController extends AbstractController
{
    /**
     * @param Player    $player
     * @return mixed
     */
    public function playerChartStatus(Player $player): mixed
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus')
            ->getStatsFromPlayer($player);
    }
}
