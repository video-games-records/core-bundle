<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PlayerController
 * @Route("/player")
 */
class PlayerController extends AbstractController
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
     * @return mixed
     */
    public function stats()
    {
        $playerStats =  $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getStats();
        $gameStats =  $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->getStats();
        $teamStats =  $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getStats();

        return array(
            'nbPlayer' => $playerStats[1],
            'nbChart' => $playerStats[2],
            'nbChartProven' => $playerStats[3],
            'nbGame' => $gameStats[1],
            'nbTeam' => $teamStats[1],
        );
    }

    /**
     * @return mixed
     */
    public function rankingPointChart()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingPointChart($this->getPlayer());
    }

    /**
     * @return mixed
     */
    public function rankingPointGame()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingPointGame($this->getPlayer());
    }

    /**
     * @return mixed
     */
    public function rankingMedal()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingMedal($this->getPlayer());
    }

    /**
     * @return mixed
     */
    public function rankingCup()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingCup($this->getPlayer());
    }

    /**
     * @return mixed
     */
    public function rankingProof()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingProof($this->getPlayer());
    }

    /**
     * @return mixed
     */
    public function rankingBadge()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingBadge($this->getPlayer());
    }

    /**
     * @return mixed
     */
    public function rankingPointGameTop5()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingPointGame(null, 5);
    }

    /**
     * @return mixed
     */
    public function rankingCupTop5()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingCup(null, 5);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function autocomplete(Request $request)
    {
        $q = $request->query->get('query', null);
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:player')->autocomplete($q);
    }
}
