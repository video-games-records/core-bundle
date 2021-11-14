<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Service\PlayerService;

/**
 * Class PlayerController
 */
class PlayerController extends DefaultController
{
    private PlayerService $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
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
        $playerStats =  $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getStats();
        $gameStats =  $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->getStats();
        $teamStats =  $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getStats();

        return array(
            'nbPlayer' => $playerStats[1],
            'nbChart' => $playerStats[2],
            'nbChartProven' => $playerStats[3],
            'nbGame' => $gameStats[1],
            'nbTeam' => $teamStats[1],
        );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function rankingPointChart(Request $request)
    {
        $maxRank = $request->query->get('maxRank', 100);
        $idTeam = $request->query->get('idTeam', null);
        if ($idTeam) {
            $team = $this->getDoctrine()->getManager()->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $idTeam);
        } else {
            $team = null;
        }
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingPointChart($this->getPlayer(), $maxRank, $team);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function rankingPointGame(Request $request)
    {
        $maxRank = $request->query->get('maxRank', 100);
        $idTeam = $request->query->get('idTeam', null);
        if ($idTeam) {
            $team = $this->getDoctrine()->getManager()->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $idTeam);
        } else {
            $team = null;
        }
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingPointGame($this->getPlayer(), $maxRank, $team);
    }

    /**
     * @return mixed
     */
    public function rankingMedal()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingMedal($this->getPlayer());
    }

    /**
     * @return mixed
     */
    public function rankingCup()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingCup($this->getPlayer());
    }

    /**
     * @return mixed
     */
    public function rankingProof()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingProof($this->getPlayer());
    }

    /**
     * @return mixed
     */
    public function rankingBadge()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingBadge($this->getPlayer());
    }

    /**
     * @return mixed
     */
    public function rankingPointGameTop5()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingPointGame(null, 5);
    }

    /**
     * @return mixed
     */
    public function rankingCupTop5()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingCup(null, 5);
    }

    /**
     * @param Player    $player
     * @return mixed
     */
    public function playerChartStatus(Player $player)
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChartStatus')
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
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function nbLostPosition(Player $player)
    {
        return $this->playerService->getNbLostPosition($player);
    }

    /**
     * @param Player $player
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function nbNewLostPosition(Player $player)
    {
        return $this->playerService->getNbNewLostPosition($player);
    }
}
