<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Platform;

/**
 * Class PlatformController
 */
class PlatformController extends DefaultController
{
    /**
     * @param Platform $platform
     * @param Request  $request
     * @return mixed
     */
    public function playerRankingPointPlatform(Platform $platform, Request $request)
    {
        $maxRank = $request->query->get('maxRank', null);
        return $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerPlatform')->getRankingPointPlatform(
            $platform,
            $maxRank,
            $this->getPlayer()
        );
    }
}
