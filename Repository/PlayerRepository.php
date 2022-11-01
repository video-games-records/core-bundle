<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class PlayerRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    /**
     * @param $q
     * @return mixed
     */
    public function autocomplete($q)
    {
        $query = $this->createQueryBuilder('p');

        $query
            ->where('p.pseudo LIKE :q')
            ->setParameter('q', '%' . $q . '%')
            ->orderBy('p.pseudo', 'ASC');

        return $query->getQuery()->getResult();
    }

    /**
     * @param $user
     * @return mixed|Player
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function getPlayerFromUser($user)
    {
        $qb = $this->createQueryBuilder('player')
            ->where('player.user = :userId')
            ->setParameter('userId', $user->getId())
            ->addSelect('team')->leftJoin('player.team', 'team');

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Get data to maj dwh.vgr_player
     */
    public function getDataForDwh()
    {
        $query = $this->_em->createQuery("
            SELECT p.id,
                   p.chartRank0,
                   p.chartRank1,
                   p.chartRank2,
                   p.chartRank3,
                   p.pointChart,
                   p.rankPointChart,
                   p.rankMedal,
                   p.nbChart,
                   p.pointGame,
                   p.rankPointGame                   
            FROM VideoGamesRecords\CoreBundle\Entity\Player p
            WHERE p.id <> 0");
        return $query->getResult();
    }


    /**
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getStats()
    {
        $qb = $this->createQueryBuilder('player')
            ->select('COUNT(player.id), SUM(player.nbChart), SUM(player.nbChartProven)');
        $qb->where('player.nbChart > 0');

        return $qb->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * Get list who cant send scores
     */
    public function getPlayerToDisabled()
    {
        $query = $this->createQueryBuilder('p')
            ->where('(p.nbChartDisabled >= :nbChartDisabled OR (p.nbChart > :nbChart AND p.nbChart/p.nbChartProven * 300 < :percentage))')
            ->setParameter('nbChartDisabled', 30)
            ->setParameter('nbChart', 300)
            ->setParameter('percentage', 3)
            ->andWhere('p.user IN (SELECT u FROM VideoGamesRecords\CoreBundle\Entity\User\UserInterface u join u.groups g WHERE g.id = 2)');
        return $query->getQuery()->getResult();
    }

    /**
     * Get list that can now send scores
     */
    public function getPlayerToEnabled()
    {
        $query = $this->createQueryBuilder('p')
            ->where('(p.nbChartDisabled < :nbChartDisabled AND (p.nbChart > :nbChart AND p.nbChart/p.nbChartProven * 300 >= :percentage))')
            ->setParameter('nbChartDisabled', 30)
            ->setParameter('nbChart', 300)
            ->setParameter('percentage', 3)
            ->andWhere('p.user IN (SELECT u FROM VideoGamesRecords\CoreBundle\Entity\User\UserInterface u join u.groups g WHERE g.id = 9)');
        return $query->getQuery()->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getProofStats()
    {
        $query = $this->createQueryBuilder('player')
            ->select('player.id as idPlayer, player.pseudo')
            ->innerJoin('player.proofRespondings', 'proof')
            ->addSelect('COUNT(proof.id) as nb, SUBSTRING(proof.updatedAt, 1, 7) as month')
            ->where("proof.checkedAt > '2020-01-01'")
            ->orderBy('month', 'DESC')
            ->groupBy('player.id')
            ->addGroupBy('month');
        return $query->getQuery()->getResult(2);
    }
}
