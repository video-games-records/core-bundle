<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\GameTopRanking;

/**
 * @extends ServiceEntityRepository<GameTopRanking>
 *
 * @method GameTopRanking|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameTopRanking|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameTopRanking[]    findAll()
 * @method GameTopRanking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameTopRankingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameTopRanking::class);
    }

    /**
     * Find the current week ranking for a game
     *
     * @param Game $game
     * @param int $year
     * @param int $week
     * @return GameTopRanking|null
     */
    public function findCurrentWeekRanking(Game $game, int $year, int $week): ?GameTopRanking
    {
        $periodValue = sprintf('%d-W%02d', $year, $week);

        return $this->findOneBy([
            'game' => $game,
            'periodType' => GameTopRanking::PERIOD_WEEK,
            'periodValue' => $periodValue
        ]);
    }


    /**
     * Delete old rankings to keep only recent data
     *
     * @param string $periodType
     * @param string $beforePeriodValue
     * @return int Number of deleted records
     */
    public function deleteOldRankings(string $periodType, string $beforePeriodValue): int
    {
        return $this->createQueryBuilder('gtr')
            ->delete()
            ->andWhere('gtr.periodType = :periodType')
            ->andWhere('gtr.periodValue < :beforePeriodValue')
            ->setParameter('periodType', $periodType)
            ->setParameter('beforePeriodValue', $beforePeriodValue)
            ->getQuery()
            ->execute();
    }
}
