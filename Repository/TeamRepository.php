<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

/**
 * TeamRepository
 */
class TeamRepository extends EntityRepository
{

    /**
     * @param $idTeam
     */
    public function maj($idTeam)
    {
        $query = $this->_em->createQuery("
            SELECT
                 tg.idTeam,
                 SUM(tg.chartRank0) as chartRank0,
                 SUM(tg.chartRank1) as chartRank1,
                 SUM(tg.chartRank2) as chartRank2,
                 SUM(tg.chartRank3) as chartRank3,
                 SUM(pg.pointChart) as pointChart,
                 SUM(pg.pointGame) as pointGame
            FROM VideoGamesRecords\CoreBundle\Entity\teamGame tg
            WHERE tg.idTeam = :idTeam
            GROUP BY tg.idTeam");

        $query->setParameter('idTeam', $idTeam);
        $result = $query->getResult();
        $row = $result[0];

        $team = $this->_em->find('VideoGamesRecords\CoreBundle\Entity\Team', $idTeam);

        $team->setChartRank0($row['chartRank0']);
        $team->setChartRank1($row['chartRank1']);
        $team->setChartRank2($row['chartRank2']);
        $team->setChartRank3($row['chartRank3']);
        $team->setPointChart($row['pointChart']);
        $team->setPointGame($row['pointGame']);

        $this->_em->persist($team);
        $this->_em->flush($team);
    }

    /**
     *
     */
    public function majGameRank()
    {
        $data = [];

        //----- MAJ game.nbTeam
        $sql = "UPDATE vgr_game g SET nbTeam = (SELECT COUNT(idGame) FROM vgr_team_game tg WHERE tg.idGame = g.id)";
        $this->_em->getConnection()->executeUpdate($sql);

        //----- data rank0
        $query = $this->_em->createQuery("
            SELECT
                 tg.idTeam,
                 COUNT(tg.idGame) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\TeamGame tg
            JOIN tg.game g
            WHERE g.nbTeam > 1
            AND tg.rankPointChart = 1
            AND tg.nbEqual = 1
            GROUP BY tg.idTeam");

        $result = $query->getResult();
        foreach ($result as $row) {
            $data['gameRank0'][$row['idTeam']] = (int) $row['nb'];
        }

        //----- data rank1 to rank3
        $query = $this->_em->createQuery("
            SELECT
                 tg.idTeam,
                 COUNT(tg.idGame) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\TeamGame tg
            WHERE tg.rankPointChart = :rank
            GROUP BY tg.idTeam");

        for ($i = 1; $i <= 3; $i++) {
            $query->setParameter('rank', $i);
            $result = $query->getResult();
            foreach ($result as $row) {
                $data["gameRank$i"][$row['idTeam']] = (int) $row['nb'];
            }
        }

        /** @var \VideoGamesRecords\CoreBundle\Entity\Team[] $players */
        $teams = $this->findAll();

        foreach ($teams as $team) {
            $idTeam = $team->getIdTeam();

            $rank0 = (isset($data['gameRank0'][$idTeam])) ? $data['gameRank0'][$idTeam] : 0;
            $rank1 = (isset($data['gameRank1'][$idTeam])) ? $data['gameRank1'][$idTeam] : 0;
            $rank2 = (isset($data['gameRank2'][$idTeam])) ? $data['gameRank2'][$idTeam] : 0;
            $rank3 = (isset($data['gameRank3'][$idTeam])) ? $data['gameRank3'][$idTeam] : 0;

            $team->setGameRank0($rank0);
            $team->setGameRank1($rank1);
            $team->setGameRank2($rank2);
            $team->setGameRank3($rank3);
        }
        $this->getEntityManager()->flush();
    }


    /**
     * Update column rankPointChart
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
     */
    public function majRankCup()
    {
        $this->majGameRank();
        $teams = $this->findBy(array(), array('gameRank0' => 'DESC', 'gameRank1' => 'DESC', 'gameRank2' => 'DESC', 'gameRank3' => 'DESC'));

        $list = array();
        foreach ($teams as $team) {
            $list[] = $team;
        }

        Ranking::addObjectRank($list, 'rankCup', array('gameRank0', 'gameRank1', 'gameRank2', 'gameRank3'));
        $this->getEntityManager()->flush();
    }
}
