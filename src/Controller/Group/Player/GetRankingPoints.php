<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Group\Player;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player\PlayerGroupRankingProvider;
use VideoGamesRecords\CoreBundle\Entity\Group;

class GetRankingPoints extends AbstractController
{
    private RankingProviderInterface $rankingProvider;

    public function __construct(
        #[Autowire(service: PlayerGroupRankingProvider::class)]
        RankingProviderInterface $rankingProvider
    ) {
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @param Group   $group
     * @param Request $request
     * @return array
     */
    public function __invoke(Group $group, Request $request): array
    {
        return $this->rankingProvider->getRankingPoints(
            $group->getId(),
            [
                'maxRank' => $request->query->get('maxRank', '5'),
                'idTeam' => $request->query->get('idTeam'),
                'user' => $this->getUser()
            ]
        );
    }
}
