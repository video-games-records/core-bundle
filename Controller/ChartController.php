<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Tools\Score;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ChartController
 */
class ChartController extends Controller
{
    /**
     * @return \VideoGamesRecords\CoreBundle\Entity\Player|null
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
     * @return \VideoGamesRecords\CoreBundle\Entity\Team|null
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
     * @Route("/{id}/{slug}", requirements={"id": "[1-9]\d*"}, name="vgr_chart_index", methods={"GET"})
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @param string $slug
     */
    public function indexAction($id, $slug)
    {
        //@todo redirect to front
        exit;
    }

    /**
     * @param Chart    $chart
     * @param Request $request
     * @return mixed
     */
    public function playerRanking(Chart $chart, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 20);
        $ranking = $this->getDoctrine()
            ->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')
            ->getRanking($chart, $this->getPlayer(), $maxRank);


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
    public function teamRanking(Chart $chart, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamChart')->getRankingPoints($chart, $maxRank, $this->getTeam());
    }
}
