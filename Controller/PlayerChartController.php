<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartLib;
use Doctrine\DBAL\DBALException;

/**
 * Class PlayerChartController
 */
class PlayerChartController extends Controller
{

    /**
     * @deprecated
     * @param Request $request
     * @return mixed
     */
    public function getOne(Request $request)
    {
        $idPlayer = $request->query->get('idPlayer', 0);
        $idChart = $request->query->get('idChart', 0);

        $playerChart = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->getFromUnique($idPlayer, $idChart);
        if ($playerChart === null) {
            $playerChart = new PlayerChart();
            $chart = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Chart')->find($idChart);
            $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->find($idPlayer);
            $playerChart->setIdPlayerChart(-1);
            $playerChart->setChart($chart);
            $playerChart->setPlayer($player);
            foreach ($chart->getLibs() as $lib) {
                $playerChartLib = new PlayerChartLib();
                $playerChartLib->setId(-1);
                $playerChartLib->setLibChart($lib);
                $playerChart->addLib($playerChartLib);
            }
        }
        return $playerChart;
    }
}
