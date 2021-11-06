<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use DateTime;
use Doctrine\DBAL\DBALException;
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
     * @param $team
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function maj($team)
    {
        $query = $this->_em->createQuery("
            SELECT
                 t.id,
                 SUM(tg.chartRank0) as chartRank0,
                 SUM(tg.chartRank1) as chartRank1,
                 SUM(tg.chartRank2) as chartRank2,
                 SUM(tg.chartRank3) as chartRank3,
                 SUM(tg.pointChart) as pointChart,
                 SUM(tg.pointGame) as pointGame,
                 COUNT(DISTINCT tg.game) as nbGame
            FROM VideoGamesRecords\CoreBundle\Entity\TeamGame tg
            JOIN tg.team t
            WHERE tg.team = :team
            GROUP BY t.id");

        $query->setParameter('team', $team);
        $result = $query->getResult();
        if ($result) {
            $row = $result[0];

            $team->setChartRank0($row['chartRank0']);
            $team->setChartRank1($row['chartRank1']);
            $team->setChartRank2($row['chartRank2']);
            $team->setChartRank3($row['chartRank3']);
            $team->setPointChart($row['pointChart']);
            $team->setPointGame($row['pointGame']);
            $team->setNbGame($row['nbGame']);

            $this->_em->persist($team);
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws DBALException
     */
    public function majGameRank()
    {
        $data = [];

        //----- MAJ game.nbTeam
        $sql = 'UPDATE vgr_game g SET nbTeam = (SELECT COUNT(idGame) FROM vgr_team_game tg WHERE tg.idGame = g.id)';
        $this->_em->getConnection()->executeUpdate($sql);

        //----- data rank0
        $query = $this->_em->createQuery("
            SELECT
                 t.id,
                 COUNT(tg.game) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\TeamGame tg
            JOIN tg.game g
            JOIN tg.team t
            WHERE g.nbTeam > 1
            AND tg.rankPointChart = 1
            AND tg.nbEqual = 1
            GROUP BY t.id");

        $result = $query->getResult();
        foreach ($result as $row) {
            $data['gameRank0'][$row['id']] = (int) $row['nb'];
        }

        //----- data rank1 to rank3
        $query = $this->_em->createQuery("
            SELECT
                 t.id,
                 COUNT(tg.game) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\TeamGame tg
            JOIN tg.team t
            WHERE tg.rankPointChart = :rank
            GROUP BY t.id");

        for ($i = 1; $i <= 3; $i++) {
            $query->setParameter('rank', $i);
            $result = $query->getResult();
            foreach ($result as $row) {
                $data["gameRank$i"][$row['id']] = (int) $row['nb'];
            }
        }

        /** @var Team[] $teams */
        $teams = $this->findAll();

        foreach ($teams as $team) {
            $idTeam = $team->getId();

            $rank0 = isset($data['gameRank0'][$idTeam]) ? $data['gameRank0'][$idTeam] : 0;
            $rank1 = isset($data['gameRank1'][$idTeam]) ? $data['gameRank1'][$idTeam] : 0;
            $rank2 = isset($data['gameRank2'][$idTeam]) ? $data['gameRank2'][$idTeam] : 0;
            $rank3 = isset($data['gameRank3'][$idTeam]) ? $data['gameRank3'][$idTeam] : 0;

            $team->setGameRank0($rank0);
            $team->setGameRank1($rank1);
            $team->setGameRank2($rank2);
            $team->setGameRank3($rank3);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
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
     * Update column rankPointChart
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function majRankPointChart()
    {
        $teams = $this->findBy(array(), array('pointChart' => 'DESC'));

        $list = array();
        foreach ($teams as $team) {
            $list[] = $team;
        }

        Ranking::addObjectRank($list, 'rankPointChart', array('pointChart'));
        $this->getEntityManager()->flush();
    }

    /**
     * Update column rankPointGame
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function majRankPointGame()
    {
        $teams = $this->findBy(array(), array('pointGame' => 'DESC'));

        $list = array();
        foreach ($teams as $team) {
            $list[] = $team;
        }

        Ranking::addObjectRank($list, 'rankPointGame', array('pointGame'));
        $this->getEntityManager()->flush();
    }

    /**
     * Update column rankMedal
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function majRankMedal()
    {
        $teams = $this->findBy(array(), array('chartRank0' => 'DESC', 'chartRank1' => 'DESC', 'chartRank2' => 'DESC', 'chartRank3' => 'DESC'));

        $list = array();
        foreach ($teams as $team) {
            $list[] = $team;
        }

        Ranking::addObjectRank($list, 'rankMedal', array('chartRank0', 'chartRank1', 'chartRank2', 'chartRank3'));
        $this->getEntityManager()->flush();
    }

    /**
     * Update column rankCup
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function majRankCup()
    {
        $teams = $this->findBy(array(), array('gameRank0' => 'DESC', 'gameRank1' => 'DESC', 'gameRank2' => 'DESC', 'gameRank3' => 'DESC'));

        $list = array();
        foreach ($teams as $team) {
            $list[] = $team;
        }

        Ranking::addObjectRank($list, 'rankCup', array('gameRank0', 'gameRank1', 'gameRank2', 'gameRank3'));
        $this->getEntityManager()->flush();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
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
    private function getRanking($column, $team = null, $maxRank = 100)
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
