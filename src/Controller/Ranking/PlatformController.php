<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player\PlayerPlatformRankingQuery;

/**
 * Class PlaformController
 */
class PlatformController extends AbstractController
{
    private PlayerPlatformRankingQuery $playerPlatformRankingQuery;

    public function __construct(PlayerPlatformRankingQuery $playerPlatformRankingQuery)
    {
        $this->playerPlatformRankingQuery = $playerPlatformRankingQuery;
    }

    /**
     * @param Platform $platform
     * @param Request  $request
     * @return array
     */
    public function getRankingPoints(Platform $platform, Request $request): array
    {
        return $this->playerPlatformRankingQuery->getRankingPoints(
            $platform->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 100),
            ]
        );
    }
}
