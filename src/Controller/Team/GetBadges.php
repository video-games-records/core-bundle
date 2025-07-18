<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Team;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Enum\BadgeType;
use VideoGamesRecords\CoreBundle\Repository\TeamBadgeRepository;

class GetBadges extends AbstractController
{
    public function __construct(
        private readonly TeamBadgeRepository $teamBadgeRepository
    ) {
    }

    public function __invoke(Team $team): array
    {
        $result = [];

        $result['master'] = $this->teamBadgeRepository->findByTeamAndType(
            $team,
            BadgeType::MASTER->value,
            ['tb.mbOrder' => 'ASC']
        );

        $result['serie'] = $this->teamBadgeRepository->findByTeamAndType(
            $team,
            BadgeType::SERIE->value,
            ['tb.createdAt' => 'ASC']
        );

        return $result;
    }
}
