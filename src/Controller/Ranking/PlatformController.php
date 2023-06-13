<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\Entity\Platform;

/**
 * Class PlaformController
 */
class PlatformController extends AbstractController
{
    private RankingProviderInterface $rankingProvider;

    public function __construct(RankingProviderInterface $rankingProvider)
    {
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @param Platform $platform
     * @param Request  $request
     * @return array
     */
    public function getRankingPoints(Platform $platform, Request $request): array
    {
        return $this->rankingProvider->getRankingPoints(
            $platform->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 100),
            ]
        );
    }
}
