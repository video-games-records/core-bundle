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
            ->innerJoin('player.proofRespondings', 'proof')
            ->addSelect('COUNT(proof.id) as nb, SUBSTRING(proof.updatedAt, 1, 7) as month')
            ->where("proof.checkedAt > '2020-01-01'")
            ->orderBy('month', 'DESC')
            ->groupBy('player.id')
            ->addGroupBy('month');
        return $query->getQuery()->getResult(2);
    }

    /**
     * @throws Exception
     */
    public function majNbChartDisabled()
    {
        // MAJ nbChartDisabled
        $sql = "UPDATE vgr_player p SET p.nbChartDisabled = (SELECT COUNT(id) FROM vgr_player_chart WHERE idStatus = 7 AND idPlayer = p.id)";
        $this->_em->getConnection()->executeStatement($sql);
    }
}
