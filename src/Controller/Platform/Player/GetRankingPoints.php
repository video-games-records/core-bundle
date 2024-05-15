<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Platform\Player;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Ranking\Provider\Player\PlayerPlatformRankingProvider;

class GetRankingPoints extends AbstractController
{
    private RankingProviderInterface $rankingProvider;

    public function __construct(
        #[Autowire(service: PlayerPlatformRankingProvider::class)]
        RankingProviderInterface $rankingProvider
    ) {
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @param Platform $platform
     * @param Request  $request
     * @return array
     */
    public function __invoke(Platform $platform, Request $request): array
    {
        return $this->rankingProvider->getRankingPoints(
            $platform->getId(),
            [
                'maxRank' => $request->query->get('maxRank', '100'),
            ]
        );
    }
}
