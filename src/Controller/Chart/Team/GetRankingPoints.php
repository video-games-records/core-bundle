<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Chart\Team;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Ranking\Provider\Team\TeamChartRankingProvider;

class GetRankingPoints extends AbstractController
{
    private RankingProviderInterface $rankingProvider;

    public function __construct(
        #[Autowire(service: TeamChartRankingProvider::class)]
        RankingProviderInterface $rankingProvider
    ) {
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @param Chart   $chart
     * @param Request $request
     * @return array
     */
    public function __invoke(Chart $chart, Request $request): array
    {
        return $this->rankingProvider->getRankingPoints(
            $chart->getId(),
            [
                'maxRank' => $request->query->get('maxRank', '5'),
                'user' => $this->getUser()
            ]
        );
    }
}
