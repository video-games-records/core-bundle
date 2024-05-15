<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Team;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Ranking\Provider\Team\TeamRankingProvider;

class GetRankingPointChart extends AbstractController
{
    private TeamRankingProvider $teamRankingProvider;

    public function __construct(TeamRankingProvider $teamRankingProvider)
    {
        $this->teamRankingProvider = $teamRankingProvider;
    }

    /**
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function __invoke(Request $request): array
    {
        return $this->teamRankingProvider->getRankingPointChart(
            [
                'maxRank' => $request->query->get('maxRank', '5'),
            ]
        );
    }
}
