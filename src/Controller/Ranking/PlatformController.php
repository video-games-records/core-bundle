<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\PlatformRankingQuery;

/**
 * Class PlaformController
 */
class PlatformController extends AbstractController
{
    private PlatformRankingQuery $platformRankingQuery;

    public function __construct(PlatformRankingQuery $platformRankingQuery)
    {
        $this->platformRankingQuery = $platformRankingQuery;
    }

    /**
     * @param Platform $platform
     * @param Request  $request
     * @return array
     * @throws ORMException
     */
    public function getRankingPoints(Platform $platform, Request $request): array
    {
        return $this->platformRankingQuery->getRankingPoints(
            $platform->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }
}
