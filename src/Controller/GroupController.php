<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Group;

/**
 * Class GroupController
 */
class GroupController extends DefaultController
{
    /**
     * @param Group   $group
     * @param Request $request
     * @return mixed
     */
    public function topScore(Group $group, Request $request)
    {
        $player = $this->getPlayer();
        $locale = $request->getLocale();
        $charts = $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\Chart')->getTopScore($group, $player, $locale);
        foreach ($charts as $chart) {
            foreach ($chart->getPlayerCharts() as $playerChart) {
                if ($playerChart->getRank() == 1) {
                    $chart->setPlayerChart1($playerChart);
                }
                if (($player !== null) && ($playerChart->getPlayer()->getId() == $player->getId())) {
                    $chart->setPlayerChartP($playerChart);
                }
            }
        }
        return $charts;
    }
}
