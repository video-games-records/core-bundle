<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Team;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TeamController
 * @Route("/team")
 */
class TeamController extends AbstractController
{
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
     * @return mixed
     */
    public function rankingPointChart()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingPointChart($this->getTeam());
    }

    /**
     * @return mixed
     */
    public function rankingPointGame()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingPointGame($this->getTeam());
    }

    /**
     * @return mixed
     */
    public function rankingMedal()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingMedal($this->getTeam());
    }

    /**
     * @return mixed
     */
    public function rankingCup()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingCup($this->getTeam());
    }

    /**
     * @return mixed
     */
    public function rankingBadge()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingBadge($this->getTeam());
    }

    /**
     * @return mixed
     */
    public function rankingPointGameTop5()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingPointGame(null, 5);
    }

    /**
     * @return mixed
     */
    public function rankingCupTop5()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingCup(null, 5);
    }
}
