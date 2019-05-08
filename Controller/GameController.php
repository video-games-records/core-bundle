<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Class GameController
 */
class GameController extends Controller
{

    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return Player|null
     */
    private function getPlayer()
    {
        if ($this->getUser() !== null) {
            return $this->getUser()->getPlayer();
        }
        return null;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function listByLetter(Request $request)
    {
        $letter = $request->query->get('letter', '0');
        $locale = $request->query->get('locale', 'en');
        $games = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')
            ->findWithLetter($letter, $locale)
            ->getResult();
        return $games;
    }


    /**
     * @param Game    $game
     * @param Request $request
     * @return mixed
     */
    public function playerRankingPoints(Game $game, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idPlayer = $request->query->get('idPlayer', null);
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingPoints($game->getId(), $maxRank, $this->getPlayer());
        return $ranking;
    }


    /**
     * @param Game    $game
     * @param Request $request
     * @return mixed
     */
    public function playerRankingMedals(Game $game, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idPlayer = $request->query->get('idPlayer', null);
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingMedals($game->getId(), $maxRank, $this->getPlayer());
        return $ranking;
    }


    /**
     * @param Game    $game
     * @param Request $request
     * @return mixed
     */
    public function teamRankingPoints(Game $game, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idPlayer = $request->query->get('idPlayer', null);
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsTeamBundle:TeamGame')->getRankingPoints($game->getId(), $maxRank, $this->getPlayer());
        return $ranking;
    }


    /**
     * @param Game    $game
     * @param Request $request
     * @return mixed
     */
    public function teamRankingMedals(Game $game, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idPlayer = $request->query->get('idPlayer', null);
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsTeamBundle:TeamGame')->getRankingMedals($game->getId(), $maxRank, $this->getPlayer());
        return $ranking;
    }
}
