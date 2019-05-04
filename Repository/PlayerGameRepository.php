<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class PlayerGameRepository extends EntityRepository
{
    /**
     * @param int $idGame
     * @param int $maxRank
     * @param int $idPlayer
     * @return \VideoGamesRecords\CoreBundle\Entity\PlayerGame[]
     */
    public function getRankingPoints($idGame, $maxRank = null, $idPlayer = null)
    {
        $query = $this->createQueryBuilder('pg')
            ->join('pg.player', 'p')
            ->join('pg.game', 'g')
            ->addSelect('p')//----- for using ->getPlayer() on each result
            ->orderBy('pg.rankPointChart');

        $query->where('g.id = :idGame')
            ->setParameter('idGame', $idGame);

        if (($maxRank !== null) && ($idPlayer !== null)) {
            $query->andWhere('(pg.rankPointChart <= :maxRank OR p.id = :idPlayer)')
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
            ->join('pg.game', 'g')
            ->addSelect('p')//----- for using ->getPlayer() on each result
            ->orderBy('pg.rankMedal');

        $query->where('g.id = :idGame')
            ->setParameter('idGame', $idGame);

        if (($maxRank !== null) && ($idPlayer !== null)) {
            $query->andWhere('(pg.rankMedal <= :maxRank OR p.id = :idPlayer)')
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
     * @param Game $game
     */
    public function maj($game)
    {
        //----- delete
        $query = $this->_em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\PlayerGame pg WHERE pg.game = :game');
        $query->setParameter('game', $game);
        $query->execute();

        //----- data without DLC
        $query = $this->_em->createQuery("
            SELECT
                 p.idPlayer,
                 SUM(pg.pointChart) as pointChartWithoutDlc,
                 SUM(pg.nbChart) as nbChartWithoutDlc,
                 SUM(pg.nbChartProven) as nbChartProvenWithoutDlc
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGroup pg
            JOIN pg.player p
            JOIN pg.group g
            WHERE g.game = :game
            AND g.boolDlc = 0
            GROUP BY p.idPlayer");

        $dataWithoutDlc = [];

        $query->setParameter('game', $game);
        $result = $query->getResult();
        foreach ($result as $row) {
            $dataWithoutDlc[$row['idPlayer']] = $row;
        }

        //----- select ans save result in array
        $query = $this->_em->createQuery("
            SELECT
                p.idPlayer,
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
            JOIN pg.player p
            JOIN pg.group g
            WHERE g.game = :game
            GROUP BY p.idPlayer
            ORDER BY pointChart DESC");


        $query->setParameter('game', $game);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $row = array_merge($row, $dataWithoutDlc[$row['idPlayer']]);
            $list[] = $row;
        }

        //----- add some data
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart'], true);
        $list = Ranking::calculateGamePoints($list, ['rankPointChart', 'nbEqual'], 'pointGame', 'pointChart');
        $list = Ranking::order($list, ['chartRank0' => SORT_DESC, 'chartRank1' => SORT_DESC, 'chartRank2' => SORT_DESC, 'chartRank3' => SORT_DESC]);
        $list = Ranking::addRank($list, 'rankMedal', ['chartRank0', 'chartRank1', 'chartRank2', 'chartRank3', 'chartRank4', 'chartRank5']);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

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
