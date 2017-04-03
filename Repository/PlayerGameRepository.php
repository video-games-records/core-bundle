<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class PlayerGameRepository extends EntityRepository
{
    /**
     * @param int $idGame
     * @param int $maxRank
     * @param int $idPlayer
     * @return array
     */
    public function getRankingPoints($idGame, $maxRank = null, $idPlayer = null)
    {
        $query = $this->createQueryBuilder('pg')
            ->join('pg.player', 'p')
            ->addSelect('p')//----- for using ->getPlayer() on each result
            ->orderBy('pg.rankPointChart');

        $query->where('pg.idGame= :idGame')
            ->setParameter('idGame', $idGame);

        if (($maxRank !== null) && ($idPlayer !== null)) {
            $query->andWhere('(pg.rankPointChart <= :maxRank OR pg.idPlayer = :idPlayer)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('idPlayer', $idPlayer);
        } elseif ($maxRank !== null) {
            $query->andWhere('pg.rankPointChart <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @param int $idGame
     * @param int $maxRank
     * @param int $idPlayer
     * @return array
     */
    public function getRankingMedals($idGame, $maxRank = null, $idPlayer = null)
    {
        $query = $this->createQueryBuilder('pg')
            ->join('pg.player', 'p')
            ->addSelect('p')//----- for using ->getPlayer() on each result
            ->orderBy('pg.rankMedal');

        $query->where('pg.idGame = :idGame')
            ->setParameter('idGame', $idGame);

        if (($maxRank !== null) && ($idPlayer !== null)) {
            $query->andWhere('(pg.rankMedal <= :maxRank OR pg.idPlayer = :idPlayer)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('idPlayer', $idPlayer);
        } elseif ($maxRank !== null) {
            $query->andWhere('pg.rankMedal <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param int $idGame
     */
    public function maj($idGame)
    {
        //----- delete
        $query = $this->_em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\PlayerGame pg WHERE pg.idGame = :idGame');
        $query->setParameter('idGame', $idGame);
        $query->execute();

        //----- data without DLC
        $query = $this->_em->createQuery("
            SELECT
                 pg.idPlayer,
                 SUM(pg.pointChart) as pointChartWithoutDlc,
                 SUM(pg.nbChart) as nbChartWithoutDlc,
                 SUM(pg.nbChartProven) as nbChartProvenWithoutDlc
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGroup pg
            JOIN pg.group g
            WHERE g.idGame = :idGame
            AND g.boolDlc = 0
            GROUP BY pg.idPlayer");

        $dataWithoutDlc = [];

        $query->setParameter('idGame', $idGame);
        $result = $query->getResult();
        foreach ($result as $row) {
            $dataWithoutDlc[$row['idPlayer']] = $row;
        }

        //----- select ans save result in array
        $query = $this->_em->createQuery("
            SELECT
                pg.idPlayer,
                (g.idGame) as idGame,
                '' as rankPointChart,
                '' as rankMedal,
                SUM(pg.chartRank0) as chartRank0,
                SUM(pg.chartRank1) as chartRank1,
                SUM(pg.chartRank2) as chartRank2,
                SUM(pg.chartRank3) as chartRank3,
                SUM(pg.chartRank4) as chartRank4,
                SUM(pg.chartRank5) as chartRank5,
                SUM(pg.pointChart) as pointChart,
                SUM(pg.nbChart) as nbChart,
                SUM(pg.nbChartProven) as nbChartProven
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGroup pg
            JOIN pg.group g
            WHERE g.idGame = :idGame
            GROUP BY pg.idPlayer
            ORDER BY pointChart DESC");


        $query->setParameter('idGame', $idGame);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $row = array_merge($row, $dataWithoutDlc[$row['idPlayer']]);
            $list[] = $row;
        }

        //----- add some data
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart'], true);
        $list = Ranking::calculateGamePoints($list, ['rankPointChart', 'nbEqual'], 'pointGame', 'pointChart');
        $list = Ranking::order($list, ['chartRank0' => 'DESC', 'chartRank1' => 'DESC', 'chartRank2' => 'DESC', 'chartRank3' => 'DESC']);
        $list = Ranking::addRank($list, 'rankMedal', ['chartRank0', 'chartRank1', 'chartRank2', 'chartRank3', 'chartRank4', 'chartRank5']);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        $game = $this->_em->find('VideoGamesRecords\CoreBundle\Entity\Game', $idGame);

        foreach ($list as $row) {
            $playerGame = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\PlayerGame'
            );
            $playerGame->setPlayer($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $row['idPlayer']));
            $playerGame->setGame($game);

            $this->_em->persist($playerGame);
        }
        $this->_em->flush();
    }
}
