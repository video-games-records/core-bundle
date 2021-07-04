<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class PlayerRepository extends EntityRepository
{
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
     * @param $player
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function maj($player)
    {
        // query (with boolRanking = true)
        $query = $this->_em->createQuery("
            SELECT
                 p.id,
                 SUM(pg.chartRank0) as chartRank0,
                 SUM(pg.chartRank1) as chartRank1,
                 SUM(pg.chartRank2) as chartRank2,
                 SUM(pg.chartRank3) as chartRank3,
                 SUM(pg.nbChart) as nbChart,
                 SUM(pg.nbChartProven) as nbChartProven,
                 SUM(pg.pointChart) as pointChart,
                 SUM(pg.pointGame) as pointGame,
                 COUNT(DISTINCT pg.game) as nbGame
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGame pg
            JOIN pg.player p
            JOIN pg.game g
            WHERE pg.player = :player
            AND g.boolRanking = 1
            GROUP BY p.id");
        $query->setParameter('player', $player);
        $result = $query->getResult();
        $row1 = $result[0];

        $player->setChartRank0($row1['chartRank0']);
        $player->setChartRank1($row1['chartRank1']);
        $player->setChartRank2($row1['chartRank2']);
        $player->setChartRank3($row1['chartRank3']);
        $player->setNbChart($row1['nbChart']);
        $player->setNbChartProven($row1['nbChartProven']);
        $player->setNbGame($row1['nbGame']);
        $player->setPointChart($row1['pointChart']);
        $player->setPointGame($row1['pointGame']);
        $player->setBoolMaj(false);

        $this->_em->persist($player);
        $this->_em->flush();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function majGameRank()
    {
        $data = [];

        //----- data rank0
        $query = $this->_em->createQuery("
            SELECT
                 p.id,
                 COUNT(pg.game) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGame pg
            JOIN pg.game g
            JOIN pg.player p
            WHERE pg.rankPointChart = 1
            AND g.nbPlayer > 1
            AND g.boolRanking = 1
            AND pg.nbEqual = 1
            GROUP BY p.id");

        $result = $query->getResult();
        foreach ($result as $row) {
            $data['gameRank0'][$row['id']] = (int) $row['nb'];
        }

        //----- data rank1 to rank3
        $query = $this->_em->createQuery("
            SELECT
                 p.id,
                 COUNT(pg.game) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGame pg
            JOIN pg.game g
            JOIN pg.player p
            WHERE pg.rankPointChart = :rank
            AND g.boolRanking = 1
            GROUP BY p.id");

        for ($i = 1; $i <= 3; $i++) {
            $query->setParameter('rank', $i);
            $result = $query->getResult();
            foreach ($result as $row) {
                $data["gameRank$i"][$row['id']] = (int) $row['nb'];
            }
        }

        /** @var Player[] $players */
        $players = $this->findAll();

        foreach ($players as $player) {
            $idPlayer = $player->getId();

            $rank0 = isset($data['gameRank0'][$idPlayer]) ? $data['gameRank0'][$idPlayer] : 0;
            $rank1 = isset($data['gameRank1'][$idPlayer]) ? $data['gameRank1'][$idPlayer] : 0;
            $rank2 = isset($data['gameRank2'][$idPlayer]) ? $data['gameRank2'][$idPlayer] : 0;
            $rank3 = isset($data['gameRank3'][$idPlayer]) ? $data['gameRank3'][$idPlayer] : 0;

            $player->setGameRank0($rank0);
            $player->setGameRank1($rank1);
            $player->setGameRank2($rank2);
            $player->setGameRank3($rank3);
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
        $players = $this->findBy(array(), array('pointChart' => 'DESC'));

        Ranking::addObjectRank($players, 'rankPointChart', array('pointChart'));
        $this->getEntityManager()->flush();
    }

    /**
     * Update column rankPointGame
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function majRankPointGame()
    {
        $players = $this->findBy(array(), array('pointGame' => 'DESC'));

        Ranking::addObjectRank($players, 'rankPointGame', array('pointGame'));
        $this->getEntityManager()->flush();
    }

    /**
     * Update column rankMedal
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function majRankMedal()
    {
        $players = $this->findBy(array(), array('chartRank0' => 'DESC', 'chartRank1' => 'DESC', 'chartRank2' => 'DESC', 'chartRank3' => 'DESC'));

        Ranking::addObjectRank($players, 'rankMedal', array('chartRank0', 'chartRank1', 'chartRank2', 'chartRank3'));
        $this->getEntityManager()->flush();
    }

    /**
     * Update column rankCup
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function majRankCup()
    {
        $players = $this->findBy(array(), array('gameRank0' => 'DESC', 'gameRank1' => 'DESC', 'gameRank2' => 'DESC', 'gameRank3' => 'DESC'));

        Ranking::addObjectRank($players, 'rankCup', array('gameRank0', 'gameRank1', 'gameRank2', 'gameRank3'));
        $this->getEntityManager()->flush();
    }

    /**
     * Update column rankProof
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function majRankProof()
    {
        $players = $this->findBy(array(), array('nbChartProven' => 'DESC'));

        Ranking::addObjectRank($players, 'rankProof', array('nbChartProven'));
        $this->getEntityManager()->flush();
    }


    /**
     * @param $country
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function majRankCountry($country)
    {
        $players = $this->findBy(array('country' => $country), array('rankPointChart' => 'ASC'));

        Ranking::addObjectRank($players, 'rankCountry', array('rankPointChart'));
        $this->getEntityManager()->flush();
    }


    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function majRankBadge()
    {
        $players = $this->findBy(array(), array('pointBadge' => 'DESC', 'nbMasterBadge' => 'DESC'));

        Ranking::addObjectRank($players, 'rankBadge', array('pointBadge', 'nbMasterBadge'));
        $this->getEntityManager()->flush();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function majPointBadge()
    {
        // MAJ nbPlayer badge
        $sql = "UPDATE vgr_badge b
        SET nbPlayer = (
            SELECT COUNT(id)
            FROM vgr_player_badge
            WHERE idBadge = b.id AND ended_at IS NULL
            )";
        $this->_em->getConnection()->executeStatement($sql);

        // MAJ value badge
        $sql = "UPDATE vgr_badge, vgr_game
        SET vgr_badge.value = FLOOR(
            100 * (
                6250 * ( -1 / ( 100 + vgr_game.nbPlayer - vgr_badge.nbPlayer) + 0.0102) / ( POW( vgr_badge.nbPlayer, 1 / 3 ) )
            )
        )
        WHERE vgr_badge.id = vgr_game.idBadge
        AND vgr_badge.nbPlayer > 0";
        $this->_em->getConnection()->executeStatement($sql);


        //----- data
        $data = [];
        $query = $this->_em->createQuery("
            SELECT
                 p.id,
                 COUNT(pb.badge) as nbMasterBadge,
                 SUM(b.value) as pointBadge
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerBadge pb
            JOIN pb.badge b
            JOIN b.game g
            JOIN pb.player p
            WHERE b.type = :type
            AND pb.ended_at IS NULL
            AND g.boolRanking = 1
            GROUP BY p.id");
        $query->setParameter('type', 'Master');
        $result = $query->getResult();
        foreach ($result as $row) {
            $data['nbMasterBadge'][$row['id']] = (int) $row['nbMasterBadge'];
            $data['pointBadge'][$row['id']] = (int) $row['pointBadge'];
        }

        /** @var Player[] $players */
        $players = $this->findAll();

        foreach ($players as $player) {
            $idPlayer = $player->getId();

            $nbMasterBadge = isset($data['nbMasterBadge'][$idPlayer]) ? $data['nbMasterBadge'][$idPlayer] : 0;
            $pointBadge = isset($data['pointBadge'][$idPlayer]) ? $data['pointBadge'][$idPlayer] : 0;

            $player->setNbMasterBadge($nbMasterBadge);
            $player->setPointBadge($pointBadge);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param null $player
     * @param int  $maxRank
     * @param null $team
     * @return array
     */
    public function getRankingPointChart($player = null, $maxRank = 100, $team = null)
    {
        return $this->getRanking('rankPointChart', $player, $maxRank, $team);
    }


    /**
     * @param Player|null $player
     * @param int         $maxRank
     * @param Team|null   $team
     * @return int|mixed|string
     */
    public function getRankingPointGame(Player $player = null, int $maxRank = 100, Team $team = null)
    {
        return $this->getRanking('rankPointGame', $player, $maxRank, $team);
    }


    /**
     * @param null $player
     * @return array
     */
    public function getRankingMedal($player = null)
    {
        return $this->getRanking('rankMedal', $player);
    }


    /**
     * @param null $player
     * @param int  $maxRank
     * @return int|mixed|string
     */
    public function getRankingCup($player = null, int $maxRank = 100)
    {
        return $this->getRanking('rankCup', $player, $maxRank);
    }

    /**
     * @param null $player
     * @return array
     */
    public function getRankingProof($player = null)
    {
        return $this->getRanking('rankProof', $player);
    }

    /**
     * @param null $player
     * @return array
     */
    public function getRankingBadge($player = null)
    {
        return $this->getRanking('rankBadge', $player);
    }

    /**
     * @param      $country
     * @param null $maxRank
     * @return array
     */
    public function getRankingCountry($country, $maxRank = null)
    {
        $query = $this->createQueryBuilder('p')
            ->where('(p.country = :country)')
            ->setParameter('country', $country)
            ->orderBy('p.rankCountry');

        if ($maxRank !== null) {
            $query->andWhere('p.rankCountry <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults($maxRank);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param      $column
     * @param null $player
     * @param int  $maxRank
     * @param null $team
     * @return int|mixed|string
     */
    private function getRanking($column, $player = null, int $maxRank = 100, $team = null)
    {
        $query = $this->createQueryBuilder('p')
            ->orderBy("p.$column");

        if ($team !== null) {
            $query->andWhere('(p.team = :team)')
                ->setParameter('team', $team);
        } elseif ($player !== null) {
            $query->where("(p.$column <= :maxRank OR p = :player)")
                ->setParameter('maxRank', 100)
                ->setParameter('player', $player);
        } else {
            $query->where("p.$column <= :maxRank")
                ->setParameter('maxRank', $maxRank);
        }
        return $query->getQuery()->getResult();
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
            ->innerJoin('player.proof', 'proof')
            ->addSelect('COUNT(proof.id) as nb, SUBSTRING(proof.updatedAt, 1, 7) as month')
            ->where("proof.checkedAt > '2020-01-01'")
            ->orderBy('month', 'DESC')
            ->groupBy('player.id')
            ->addGroupBy('month');
        return $query->getQuery()->getResult(2);
    }
}
