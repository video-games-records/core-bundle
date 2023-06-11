<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingQueryInterface;
use VideoGamesRecords\CoreBundle\Entity\Platform;

/**
 * Class PlaformController
 */
class PlatformController extends AbstractController
{
    private RankingQueryInterface $rankingQuery;

    public function __construct(RankingQueryInterface $rankingQuery)
    {
        $this->rankingQuery = $rankingQuery;
    }

    /**
     * @param Platform $platform
     * @param Request  $request
     * @return array
     */
    public function getRankingPoints(Platform $platform, Request $request): array
    {
        return $this->rankingQuery->getRankingPoints(
            $platform->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 100),
            ]
        );
    }
}
