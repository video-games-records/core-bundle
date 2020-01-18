<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Class PlayerController
 * @Route("/player")
 */
class PlayerController extends Controller
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

        return array(
            'nbPlayer' => $playerStats[1],
            'nbChart' => $playerStats[2],
            'nbChartProven' => $playerStats[3],
            'nbGame' => $gameStats[1],
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
}
