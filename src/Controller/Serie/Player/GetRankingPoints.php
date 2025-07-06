<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Serie\Player;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player\PlayerSerieRankingProvider;
use VideoGamesRecords\CoreBundle\Entity\Serie;

class GetRankingPoints extends AbstractController
{
    private RankingProviderInterface $rankingProvider;

    public function __construct(
        #[Autowire(service: PlayerSerieRankingProvider::class)]
        RankingProviderInterface $rankingProvider
    ) {
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @param Serie   $serie
     * @param Request $request
     * @return array
     */
    public function __invoke(Serie $serie, Request $request): array
    {
        return $this->rankingProvider->getRankingPoints(
            $serie->getId(),
            [
                'maxRank' => $request->query->get('maxRank', '100'),
                'limit' => $request->query->get('limit', '1000'),
                'user' => $this->getUser()
            ]
        );
    }
}
