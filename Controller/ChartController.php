<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\Tools\Score;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartLib;

/**
 * Class ChartController
 */
class ChartController extends DefaultController
{
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
        $charts = $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\Chart')->getList(
            $page,
            $this->getPlayer(),
            $search,
            $locale,
            $itemsPerPage
        );
        // IF NOT EXIST => Create a playerChart with id=-1 AND value = null
        $game = $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')->find($search['idGame']);
        $platforms = $game->getPlatforms();
        foreach ($charts as $chart) {
            if (count($chart->getPlayerCharts()) == 0) {
                $playerChart = new PlayerChart();
                $player = $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')->find($this->getPlayer());
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
                $chart->addPlayerChart($playerChart);
            } else {
                // Set lastUpdate now for return put call
                $playerCharts = $chart->getPlayerCharts();
                $playerCharts[0]->setLastUpdate(new DateTime());
            }
        }
        return $charts;
    }
}
