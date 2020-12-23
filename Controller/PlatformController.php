<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\Player;

/**
 * Class GameController
 * @Route("/game")
 */
class PlatformController extends AbstractController
{
    /**
     * @return Player|null
     */
    private function getPlayer(): ?Player
    {
        if ($this->getUser() !== null) {
            return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
                ->getPlayerFromUser($this->getUser());
        }
        return null;
    }

    /**
     * @param Platform $platform
     * @param Request  $request
     * @return mixed
     */
    public function playerRankingPointChart(Platform $platform, Request $request)
    {
        $maxRank = $request->query->get('maxRank', null);
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerPlatform')->getRankingPointChart(
            $platform,
            $maxRank,
            $this->getPlayer()
        );
    }

    /**
     * @param Platform $platform
     * @param Request  $request
     * @return mixed
     */
    public function playerRankingPointGame(Platform $platform, Request $request)
    {
        $maxRank = $request->query->get('maxRank', null);
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerPlatform')->getRankingPointGame(
            $platform,
            $maxRank,
            $this->getPlayer()
        );
    }
}
