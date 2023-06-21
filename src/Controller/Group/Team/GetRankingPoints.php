<?php

namespace VideoGamesRecords\CoreBundle\Controller\Group\Team;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\Entity\Group;

class GetRankingPoints extends AbstractController
{
    private RankingProviderInterface $rankingProvider;

    public function __construct(RankingProviderInterface $rankingProvider)
    {
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @param Group $group
     * @param Request $request
     * @return array
     */
    public function __invoke(Group $group, Request $request): array
    {
        return $this->rankingProvider->getRankingPoints(
            $group->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }
}
