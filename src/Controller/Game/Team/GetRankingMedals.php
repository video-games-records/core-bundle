<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Game\Team;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Team\TeamGameRankingProvider;
use VideoGamesRecords\CoreBundle\Entity\Game;

class GetRankingMedals extends AbstractController
{
    private RankingProviderInterface $rankingProvider;

    public function __construct(
        #[Autowire(service: TeamGameRankingProvider::class)]
        RankingProviderInterface $rankingProvider
    ) {
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function __invoke(Game $game, Request $request): array
    {
        return $this->rankingProvider->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', '5'),
                'user' => $this->getUser()
            ]
        );
    }
}
