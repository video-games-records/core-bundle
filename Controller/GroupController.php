<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Group;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Class GroupController
 */
class GroupController extends Controller
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
     * @param Group    $group
     * @param Request $request
     * @return mixed
     */
    public function playerRankingPoints(Group $group, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingPoints($group->getId(), $maxRank, $this->getPlayer());
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
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingMedals($group->getId(), $maxRank, $this->getPlayer());
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
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsTeamBundle:TeamGroup')->getRankingPoints($group->getId(), $maxRank, $this->getPlayer());
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
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsTeamBundle:TeamGroup')->getRankingMedals($group->getId(), $maxRank, $this->getPlayer());
        return $ranking;
    }
}
