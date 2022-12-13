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
    private PlayerRepository $playerRepository;

    public function __construct(PlayerRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function autocomplete(Request $request): mixed
    {
        $q = $request->query->get('query', null);
        return $this->playerRepository->autocomplete($q);
    }

    /**
     * @param Player    $player
     * @return mixed
     */
    public function playerChartStatus(Player $player)
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus')
            ->getStatsFromPlayer($player);
    }
}
