<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartLib;
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
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsTeamBundle:TeamGame')->getRankingMedals($game->getId(), $maxRank, $this->getPlayer());
        return $ranking;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return mixed
     */
    public function charts(Game $game, Request $request)
    {
        $idGroup = $request->query->get('idGroup', null);
        $idChart = $request->query->get('idChart', null);
        $libChart = $request->query->get('libChart', null);
        $charts = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Chart')->getList(
            $game->getId(),
            $this->getPlayer(),
            $idGroup,
            $idChart,
            $libChart
        );
        // IF NOT EXIST => Create a playerChart with id=-1 AND value = null
        foreach ($charts as $chart) {
            if (count($chart->getPlayerCharts()) == 0) {
                $playerChart = new PlayerChart();
                $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->find($this->getPlayer());
                $playerChart->setIdPlayerChart(-1);
                $playerChart->setChart($chart);
                $playerChart->setPlayer($player);
                foreach ($chart->getLibs() as $lib) {
                    $playerChartLib = new PlayerChartLib();
                    $playerChartLib->setId(-1);
                    $playerChartLib->setLibChart($lib);
                    $playerChart->addLib($playerChartLib);
                }
                $chart->setPlayerCharts(array($playerChart));
            }
        }

        return $charts;
    }
}
