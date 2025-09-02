<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\PlayerTopRanking;

/**
 * @extends ServiceEntityRepository<PlayerTopRanking>
 *
 * @method PlayerTopRanking|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerTopRanking|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerTopRanking[]    findAll()
 * @method PlayerTopRanking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerTopRankingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerTopRanking::class);
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
        return $this->createQueryBuilder('ptr')
            ->delete()
            ->andWhere('ptr.periodType = :periodType')
            ->andWhere('ptr.periodValue < :beforePeriodValue')
            ->setParameter('periodType', $periodType)
            ->setParameter('beforePeriodValue', $beforePeriodValue)
            ->getQuery()
            ->execute();
    }
}