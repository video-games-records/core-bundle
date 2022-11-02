<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Service\LostPositionManager;
use VideoGamesRecords\CoreBundle\Service\PlayerService;

/**
 * Class PlayerController
 */
class PlayerController extends DefaultController
{
    private PlayerService $playerService;
    private LostPositionManager $lostPositionManager;

    public function __construct(PlayerService $playerService, LostPositionManager $lostPositionManager)
    {
        $this->playerService = $playerService;
        $this->lostPositionManager = $lostPositionManager;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function autocomplete(Request $request)
    {
        $q = $request->query->get('query', null);
        return $this->playerService->autocomplete($q);
    }

    /**
     * @return array
     */
    public function stats(): array
    {
        $playerStats =  $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')->getStats();
        $gameStats =  $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')->getStats();
        $teamStats =  $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\Team')->getStats();

        return array(
            'nbPlayer' => $playerStats[1],
            'nbChart' => $playerStats[2],
            'nbChartProven' => $playerStats[3],
            'nbGame' => $gameStats[1],
            'nbTeam' => $teamStats[1],
        );
    }

    /**
     * @param Player $player
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function canAskProof(Player $player): bool
    {
        return $this->playerService->canAskProof($player);
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

    /**
     * @param Player    $player
     * @return mixed
     */
    public function gamePlayerChartStatus(Player $player)
    {
        return $this->playerService->getGameStats($player);
    }

    /**
     * @param Player $player
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getNbLostPosition(Player $player): int
    {
        return $this->lostPositionManager->getNbLostPosition($player);
    }

    /**
     * @param Player $player
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getNbNewLostPosition(Player $player): int
    {
        return $this->lostPositionManager->getNbNewLostPosition($player);
    }
}
