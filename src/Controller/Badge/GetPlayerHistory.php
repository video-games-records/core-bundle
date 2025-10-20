<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Badge;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Badge;
use VideoGamesRecords\CoreBundle\Repository\PlayerBadgeRepository;

class GetPlayerHistory extends AbstractController
{
    public function __construct(
        private readonly PlayerBadgeRepository $playerBadgeRepository
    ) {
    }

    public function __invoke(Badge $badge): array
    {
        $qb = $this->playerBadgeRepository->createQueryBuilder('pb')
            ->select('pb', 'p', 'b')
            ->join('pb.player', 'p')
            ->join('pb.badge', 'b')
            ->where('pb.badge = :badge')
            ->setParameter('badge', $badge)
            ->orderBy('pb.createdAt', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
