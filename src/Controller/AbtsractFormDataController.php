<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartLib;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

abstract class AbtsractFormDataController extends AbstractController
{
    protected UserProvider $userProvider;
    protected EntityManagerInterface $em;
    protected Game $game;

    public function __construct(UserProvider $userProvider, EntityManagerInterface $em)
    {
        $this->userProvider = $userProvider;
        $this->em = $em;
    }

    /**
     * IF NOT EXIST => Create a playerChart with id=-1 AND value = null
     * @param  $charts
     * @param  $player
     */
    protected function setScores($charts, $player)
    {
        $platforms = $this->game->getPlatforms();
        foreach ($charts as $chart) {
            if (count($chart->getPlayerCharts()) == 0) {
                $playerChart = new PlayerChart();
                $playerChart->setId(-1);
                $playerChart->setChart($chart);
                $playerChart->setPlayer($player);
                $playerChart->setLastUpdate(new \DateTime());
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
                $playerCharts[0]->setLastUpdate(new \DateTime());
            }
        }
        return $charts;
    }
}
