<?php

namespace VideoGamesRecords\CoreBundle\Controller\Chart;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\DataTransformer\UserToPlayerTransformer;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartLib;

/**
 * Class FormDatController
 * Call api for form submit scores
 * Return charts with the one relation player-chart of the connected user
 * If the user has not relation, a default relation is created
 */
class FormDataController extends AbstractController
{
    private UserToPlayerTransformer $userToPlayerTransformer;
    private EntityManagerInterface $em;
    private Game $game;

    public function __construct(UserToPlayerTransformer $userToPlayerTransformer, EntityManagerInterface $em)
    {
        $this->userToPlayerTransformer = $userToPlayerTransformer;
        $this->em = $em;
    }

    /**
     * @param Game   $game
     * @param Request $request
     * @throws ORMException
     */
    public function loadGame(Game $game, Request $request)
    {
        $this->game = $game;

        $player = $this->userToPlayerTransformer->transform($this->getUser());
        $page = (int) $request->query->get('page', 1);
        $itemsPerPage = (int) $request->query->get('itemsPerPage', 20);
        $locale = $request->getLocale();
        $search = array(
            'game' => $game,
        );

        $charts = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Chart')->getList(
            $page,
            $player,
            $search,
            $locale,
            $itemsPerPage
        );

        return $this->setScores($charts, $player);
    }


    /**
     * @param Group   $group
     * @param Request $request
     * @throws ORMException
     */
    public function loadGroup(Group $group, Request $request)
    {
        $this->game = $group->getGame();

        $player = $this->userToPlayerTransformer->transform($this->getUser());
        $page = (int) $request->query->get('page', 1);
        $itemsPerPage = (int) $request->query->get('itemsPerPage', 20);
        $locale = $request->getLocale();
        $search = array(
            'group' => $group,
        );

        $charts = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Chart')->getList(
            $page,
            $player,
            $search,
            $locale,
            $itemsPerPage
        );

        return $this->setScores($charts, $player);
    }

    /**
     * @param Chart   $chart
     * @param Request $request
     * @return mixed
     * @throws ORMException
     */
    public function loadChart(Chart $chart, Request $request): mixed
    {
        $this->game = $chart->getGroup()->getGame();

        $player = $this->userToPlayerTransformer->transform($this->getUser());
        $page = 1;
        $itemsPerPage = 20;
        $locale = $request->getLocale();
        $search = array(
            'chart' => $chart,
        );

        $charts = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Chart')->getList(
            $page,
            $player,
            $search,
            $locale,
            $itemsPerPage
        );

        return $this->setScores($charts, $player);
    }


    /**
     * IF NOT EXIST => Create a playerChart with id=-1 AND value = null
     * @param        $charts
     * @param        $player
     */
    private function setScores($charts, $player)
    {
        $platforms = $this->game->getPlatforms();
        foreach ($charts as $chart) {
            if (count($chart->getPlayerCharts()) == 0) {
                $playerChart = new PlayerChart();
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
