<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Tools\Score;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartLib;

/**
 * Class ChartController
 */
class ChartController extends AbstractController
{
    /**
     * @return Player|null
     */
    private function getPlayer()
    {
        if ($this->getUser() !== null) {
            return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
                ->getPlayerFromUser($this->getUser());
        }
        return null;
    }

    /**
     * @return Team|null
     */
    private function getTeam()
    {
        if ($this->getUser() !== null) {
            $player =  $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
                ->getPlayerFromUser($this->getUser());
            return $player->getTeam();
        }
        return null;
    }

    /**
     * @param Chart    $chart
     * @param Request $request
     * @return mixed
     */
    public function playerRanking(Chart $chart, Request $request)
    {
        if ($chart->getStatusPlayer() == Chart::STATUS_NORMAL) {
            $maxRank = $request->query->get('maxRank', 100);
            $ranking = $this->getDoctrine()
                ->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')
                ->getRanking($chart, $this->getPlayer(), $maxRank);
        } else {
            $ranking = $this->getDoctrine()
                ->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')
                ->getRankingForUpdate($chart);
        }

        for ($i=0; $i<=count($ranking)-1; $i++) {
            foreach ($chart->getLibs() as $lib) {
                $key = $lib->getIdLibChart();
                // format value
                $ranking[$i]['values'][] = Score::formatScore(
                    $ranking[$i]["value_$key"],
                    $lib->getType()->getMask()
                );
            }
        }
        return $ranking;
    }

    /**
     * @param Chart    $chart

     * @return mixed
     */
    public function playerDisableRanking(Chart $chart)
    {
        $ranking = $this->getDoctrine()
            ->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')
            ->getDisableRanking($chart);

        for ($i=0; $i<=count($ranking)-1; $i++) {
            foreach ($chart->getLibs() as $lib) {
                $key = $lib->getIdLibChart();
                // format value
                $ranking[$i]['values'][] = Score::formatScore(
                    $ranking[$i]["value_$key"],
                    $lib->getType()->getMask()
                );
            }
        }
        return $ranking;
    }




    /**
     * @param Chart    $chart
     * @param Request $request
     * @return mixed
     */
    public function playerRankingPoints(Chart $chart, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idTeam = $request->query->get('idTeam', null);
        if ($idTeam) {
            $team = $this->getDoctrine()->getManager()->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $idTeam);
        } else {
            $team = null;
        }
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->getRankingPoints($chart, $maxRank, $this->getPlayer(), $team);
    }

    /**
     * @param Chart    $chart
     * @param Request $request
     * @return mixed
     */
    public function teamRanking(Chart $chart, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamChart')->getRankingPoints($chart, $maxRank, $this->getTeam());
    }

    /**
     * Call api form form submit scores
     * Return charts with the one relation player-chart of the connected user
     * If the user has not relation, a default relation is created
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function charts(Request $request)
    {
        $page = (int) $request->query->get('page', 1);
        $itemsPerPage = (int) $request->query->get('itemsPerPage', 20);
        $locale = $request->getLocale();
        $search = array(
            'idGame' => $request->query->get('idGame', null),
            'idGroup' => $request->query->get('idGroup', null),
            'idChart' => $request->query->get('idChart', null),
            'libChart' => $request->query->get('libChart', null),
        );
        $charts = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Chart')->getList(
            $page,
            $this->getPlayer(),
            $search,
            $locale,
            $itemsPerPage
        );
        // IF NOT EXIST => Create a playerChart with id=-1 AND value = null
        $game = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->find($search['idGame']);
        $platforms = $game->getPlatforms();
        foreach ($charts as $chart) {
            if (count($chart->getPlayerCharts()) == 0) {
                $playerChart = new PlayerChart();
                $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->find($this->getPlayer());
                $playerChart->setId(-1);
                $playerChart->setChart($chart);
                $playerChart->setPlayer($player);
                $playerChart->setLastUpdate(new DateTime());
                if (count($platforms) == 1) {
                    $playerChart->setPlatform($platforms[0]);
                }
                foreach ($chart->getLibs() as $lib) {
                    $playerChartLib = new PlayerChartLib();
                    $playerChartLib->setId(-1);
                    $playerChartLib->setLibChart($lib);
                    $playerChart->addLib($playerChartLib);
                }
                $chart->setPlayerCharts(array($playerChart));
            } else {
                // Set lastUpdate now for return put call
                $playerCharts = $chart->getPlayerCharts();
                $playerCharts[0]->setLastUpdate(new DateTime());
            }
        }
        return $charts;
    }
}
