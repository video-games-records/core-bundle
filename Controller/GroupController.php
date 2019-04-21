<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Form\Type\SubmitFormFactory;

/**
 * Class GroupController
 */
class GroupController extends Controller
{


    /**
     * @param Group    $group
     * @param Request $request
     * @return mixed
     */
    public function playerRankingPoints(Group $group, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idPlayer = $request->query->get('idPlayer', null);
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingPoints($group->getId(), $maxRank, $idPlayer);
        return $ranking;
    }


    /**
     * @param Group    $group
     * @param Request $request
     * @return mixed
     */
    public function playerRankingMedals(Group $group, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idPlayer = $request->query->get('idPlayer', null);
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingMedals($group->getId(), $maxRank, $idPlayer);
        return $ranking;
    }


    /**
     * @param Group    $group
     * @param Request $request
     * @return mixed
     */
    public function teamRankingPoints(Group $group, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idPlayer = $request->query->get('idPlayer', null);
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsTeamBundle:TeamGroup')->getRankingPoints($group->getId(), $maxRank, $idPlayer);
        return $ranking;
    }


    /**
     * @param Group    $group
     * @param Request $request
     * @return mixed
     */
    public function teamRankingMedals(Group $group, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idPlayer = $request->query->get('idPlayer', null);
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsTeamBundle:TeamGroup')->getRankingMedals($group->getId(), $maxRank, $idPlayer);
        return $ranking;
    }
}
