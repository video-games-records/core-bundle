<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\Entity\Team;

/**
 * TeamRepository
 */
class TeamRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    /**
     * @return void
     */
    public function majPointBadge()
    {
        //----- data
        $data = [];
        $query = $this->_em->createQuery("
            SELECT
                 t.id,
                 COUNT(tb.badge) as nbMasterBadge,
                 SUM(b.value) as pointBadge
            FROM VideoGamesRecords\CoreBundle\Entity\TeamBadge tb
            JOIN tb.badge b
            JOIN tb.team t
            WHERE b.type = :type
            AND tb.ended_at IS NULL
            GROUP BY t.id");
        $query->setParameter('type', 'Master');
        $result = $query->getResult();
        foreach ($result as $row) {
            $data['nbMasterBadge'][$row['id']] = (int) $row['nbMasterBadge'];
            $data['pointBadge'][$row['id']] = (int) $row['pointBadge'];
        }

        /** @var Team[] $teams */
        $teams = $this->findAll();

        foreach ($teams as $team) {
            $idTeam = $team->getId();
            $nbMasterBadge = isset($data['nbMasterBadge'][$idTeam]) ? $data['nbMasterBadge'][$idTeam] : 0;
            $pointBadge = isset($data['pointBadge'][$idTeam]) ? $data['pointBadge'][$idTeam] : 0;

            $team->setNbMasterBadge($nbMasterBadge);
            $team->setPointBadge($pointBadge);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @return void
     */
    public function majRankBadge()
    {
        $teams = $this->findBy(array(), array('pointBadge' => 'DESC', 'nbMasterBadge' => 'DESC'));

        Ranking::addObjectRank($teams, 'rankBadge', array('pointBadge', 'nbMasterBadge'));
        $this->getEntityManager()->flush();
    }

    /**
     * @param null $team
     * @return array
     */
    public function getRankingPointChart($team = null)
    {
        return $this->getRanking('rankPointChart', $team);
    }

    /**
     * @param null $team
     * @param int  $maxRank
     * @return array
     */
    public function getRankingPointGame($team = null, $maxRank = 100)
    {
        return $this->getRanking('rankPointGame', $team, $maxRank);
    }


    /**
     * @param null $team
     * @return array
     */
    public function getRankingMedal($team = null)
    {
        return $this->getRanking('rankMedal', $team);
    }


    /**
     * @param null $team
     * @param int  $maxRank
     * @return array
     */
    public function getRankingCup($team = null, $maxRank = 100)
    {
        return $this->getRanking('rankCup', $team, $maxRank);
    }


    /**
     * @param null $team
     * @return array
     */
    public function getRankingBadge($team = null)
    {
        return $this->getRanking('rankBadge', $team);
    }


    /**
     * @param DateTime $date1
     * @param DateTime $date2
     * @return array
     */
    public function getNbPostDay(DateTime $date1, DateTime $date2)
    {
        $query = $this->_em->createQuery("
            SELECT
                 t.id,
                 COUNT(pc.id) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            JOIN p.team t
            WHERE pc.lastUpdate BETWEEN :date1 AND :date2
            GROUP BY t.id");


        $query->setParameter('date1', $date1);
        $query->setParameter('date2', $date2);
        $result = $query->getResult();

        $data = array();
        foreach ($result as $row) {
            $data[$row['idTeam']] = $row['nb'];
        }
        return $data;
    }

    /**
     * Get data to maj dwh.vgr_player
     */
    public function getDataForDwh()
    {
        $query = $this->_em->createQuery("
            SELECT t.id,
                   t.pointChart,
                   t.pointBadge,
                   t.chartRank0,
                   t.chartRank1,
                   t.chartRank2,
                   t.chartRank3,
                   t.rankPointChart,
                   t.rankMedal,
                   t.rankBadge,
                   t.rankCup,
                   t.gameRank0,
                   t.gameRank1,
                   t.gameRank2,
                   t.gameRank3,
                   t.nbMasterBadge,
                   t.pointGame,
                   t.rankPointGame                  
            FROM VideoGamesRecords\CoreBundle\Entity\Team t");
        return $query->getResult();
    }

    /**
     * @param      $column
     * @param null $team
     * @param int  $maxRank
     * @return int|mixed|string
     */
    private function getRanking($column, $team = null, int $maxRank = 100)
    {
        $query = $this->createQueryBuilder('t')
            ->where("(t.$column != 0)")
            ->orderBy("t.$column");

        if ($team !== null) {
            $query->andWhere("(t.$column <= :maxRank OR t = :team)")
                ->setParameter('maxRank', $maxRank)
                ->setParameter('team', $team);
        } else {
            $query->andWhere("t.$column <= :maxRank")
                ->setParameter('maxRank', $maxRank);
        }
        return $query->getQuery()->getResult();
    }


    /**
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function getStats()
    {
        $qb = $this->createQueryBuilder('team')
            ->select('COUNT(team.id)');
        $qb->where('team.pointChart > 0');

        return $qb->getQuery()
            ->getOneOrNullResult();
    }
}
