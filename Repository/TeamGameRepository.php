<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

/**
 * TeamGameRepository
 */
class TeamGameRepository extends EntityRepository
{

    /**
     * @param int $idGame
     * @param int $maxRank
     * @param int $idTeam
     * @return array
     */
    public function getRankingPoints($idGame, $maxRank = null, $idTeam = null)
    {
        $query = $this->createQueryBuilder('tg')
            ->join('tg.team', 't')
            ->addSelect('t')//----- for using ->getTeam() on each result
            ->orderBy('tg.rankPointChart');

        $query->where('tg.idGame= :idGame')
            ->setParameter('idGame', $idGame);

        if (($maxRank !== null) && ($idTeam !== null)) {
            $query->andWhere('(tg.rankPointChart <= :maxRank OR tg.idTeam = :idTeam)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('idTeam', $idTeam);
        } elseif ($maxRank !== null) {
            $query->andWhere('tg.rankPointChart <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }

        return $query->getQuery()->getResult();
    }


    /**
     * @param int $idGame
     * @param int $maxRank
     * @param int $idTeam
     * @return array
     */
    public function getRankingMedals($idGame, $maxRank = null, $idTeam = null)
    {
        $query = $this->createQueryBuilder('tg')
            ->join('tg.team', 't')
            ->addSelect('t')//----- for using ->getTeam() on each result
            ->orderBy('tg.rankMedal');

        $query->where('tg.idGame= :idGame')
            ->setParameter('idGame', $idGame);

        if (($maxRank !== null) && ($idTeam !== null)) {
            $query->andWhere('(tg.rankMedal <= :maxRank OR tg.idTeam = :idTeam)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('idTeam', $idTeam);
        } elseif ($maxRank !== null) {
            $query->andWhere('tg.rankMedal <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param $idGame
     */
    public function maj($idGame)
    {
        //----- delete
        $query = $this->_em->createQuery(
            'DELETE VideoGamesRecords\CoreBundle\Entity\TeamGame tg WHERE tg.idGame = :idGame'
        );
        $query->setParameter('idGame', $idGame);
        $query->execute();

        //----- select ans save result in array
        $query = $this->_em->createQuery("
            SELECT
                tg.idTeam,
                (g.idGame) as idGame,
                '' as rankPointChart,
                '' as rankMedal,
                SUM(tg.chartRank0) as chartRank0,
                SUM(tg.chartRank1) as chartRank1,
                SUM(tg.chartRank2) as chartRank2,
                SUM(tg.chartRank3) as chartRank3,
                SUM(tg.pointChart) as pointChart
            FROM VideoGamesRecords\CoreBundle\Entity\TeamGroup tg
            JOIN tg.group g
            WHERE g.idGame = :idGame
            GROUP BY tg.idTeam
            ORDER BY pointChart DESC");


        $query->setParameter('idGame', $idGame);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $list[] = $row;
        }

        //----- add some data
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart'], true);
        $list = Ranking::order($list, ['chartRank0' => 'DESC', 'chartRank1' => 'DESC', 'chartRank2' => 'DESC', 'chartRank3' => 'DESC']);
        $list = Ranking::addRank($list, 'rankMedal', ['chartRank0', 'chartRank1', 'chartRank2', 'chartRank3']);
        $list = Ranking::calculateGamePoints($list, array('rankPointChart', 'nbEqual'), 'pointGame', 'pointChart');

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        foreach ($list as $row) {
            $teamGame = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\TeamGame'
            );
            $teamGame->setTeam($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $row['idTeam']));
            $teamGame->setGame($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Game', $idGame));

            $this->_em->persist($teamGame);
        }
        $this->_em->flush();
    }
}
