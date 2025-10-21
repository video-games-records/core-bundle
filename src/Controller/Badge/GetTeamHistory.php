<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Badge;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Badge;
use VideoGamesRecords\CoreBundle\Repository\TeamBadgeRepository;

class GetTeamHistory extends AbstractController
{
    public function __construct(
        private readonly TeamBadgeRepository $teamBadgeRepository
    ) {
    }

    public function __invoke(Badge $badge): array
    {
        $qb = $this->teamBadgeRepository->createQueryBuilder('tb')
            ->select('tb', 't', 'b')
            ->join('tb.team', 't')
            ->join('tb.badge', 'b')
            ->where('tb.badge = :badge')
            ->setParameter('badge', $badge)
            ->orderBy('tb.createdAt', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
