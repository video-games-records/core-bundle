<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\Player;

/**
 * Class PlatformController
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
    public function playerRankingPointPlatform(Platform $platform, Request $request)
    {
        $maxRank = $request->query->get('maxRank', null);
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerPlatform')->getRankingPointPlatform(
            $platform,
            $maxRank,
            $this->getPlayer()
        );
    }
}
