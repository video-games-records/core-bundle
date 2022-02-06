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
     * @param Group    $group
     * @param Request $request
     * @return mixed
     */
    public function playerRankingPoints(Group $group, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idTeam = $request->query->get('idTeam', null);
        if ($idTeam) {
            $team = $this->getDoctrine()->getManager()->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $idTeam);
        } else {
            $team = null;
        }
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingPoints($group, $maxRank, $this->getPlayer(), $team);
    }


    /**
     * @param Group    $group
     * @param Request $request
     * @return mixed
     */
    public function playerRankingMedals(Group $group, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingMedals($group, $maxRank, $this->getPlayer());
    }


    /**
     * @param Group    $group
     * @param Request $request
     * @return mixed
     */
    public function teamRankingPoints(Group $group, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamGroup')->getRankingPoints($group, $maxRank, $this->getTeam());
    }


    /**
     * @param Group    $group
     * @param Request $request
     * @return mixed
     */
    public function teamRankingMedals(Group $group, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamGroup')->getRankingMedals($group, $maxRank, $this->getTeam());
    }

    /**
     * @param Group   $group
     * @param Request $request
     * @return mixed
     */
    public function topScore(Group $group, Request $request)
    {
        $player = $this->getPlayer();
        $locale = $request->getLocale();
        $charts = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Chart')->getTopScore($group, $player, $locale);
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
