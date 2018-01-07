<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class PlayerSerieRepository extends EntityRepository
{
    /**
     * @param int $idSerie
     * @param int $idPlayer
     * @param int $maxRank
     * @param int $limit
     * @return array
     */
    public function getRankingPoints($idSerie, $idPlayer = null, $maxRank = null, $limit = null)
    {
        $query = $this->createQueryBuilder('ps')
            ->join('ps.player', 'p')
            ->addSelect('p')
            ->orderBy('ps.rankPoint');

        $query->where('ps.idSerie = :idSerie')
            ->setParameter('idSerie', $idSerie);

        $row = null;
        if (null !== $idPlayer) {
            $row = $this->findOneBy(
                [
                    'idSerie' => $idSerie,
                    'idPlayer' => $idPlayer,
                ]
            );
        }

        if (null !== $maxRank) {
            if (null !== $row) {
                $query->andWhere('(ps.rankPoint <= :maxRank OR ps.rankPoint BETWEEN :min AND :max)')
                    ->setParameter('min', $row->getRankPoint() - 5)
                    ->setParameter('max', $row->getRankPoint() + 5);
            } else {
                $query->andWhere('ps.rankPoint <= :maxRank');
            }
            $query->setParameter('maxRank', $maxRank);
        }

        if (null !== $limit) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }


    /**
     * @param int $idSerie
     * @param int $idPlayer
     * @param int $maxRank
     * @param int $limit
     * @return array
     */
    public function getRankingMedals($idSerie, $idPlayer = null, $maxRank = null, $limit = null)
    {
        $query = $this->createQueryBuilder('ps')
            ->join('ps.player', 'p')
            ->addSelect('p')
            ->orderBy('ps.rankMedal');

        $query->where('ps.idSerie = :idSerie')
            ->setParameter('idSerie', $idSerie);

        $row = null;
        if (null !== $idPlayer) {
            $row = $this->findOneBy(
                [
                    'idSerie' => $idSerie,
                    'idPlayer' => $idPlayer,
                ]
            );
        }

        if (null !== $maxRank) {
            if (null !== $row) {
                $query->andWhere('(ps.rankMedal <= :maxRank OR ps.rankMedal BETWEEN :min AND :max)')
                    ->setParameter('min', $row->getRankPoint() - 5)
                    ->setParameter('max', $row->getRankPoint() + 5);
            } else {
                $query->andWhere('ps.rankMedal <= :maxRank');
            }
            $query->setParameter('maxRank', $maxRank);
        }

        if (null !== $limit) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param int $idSerie
     */
    public function maj($idSerie)
    {
        // Delete old data
        $query = $this->_em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\PlayerSerie us WHERE us.idSerie = :idSerie');
        $query->setParameter('idSerie', $idSerie);
        $query->execute();

        // Select data
        $query = $this->_em->createQuery("
            SELECT
                pg.idPlayer,
                (g.idSerie) as idSerie,
                '' as rankPoint,
                '' as rankMedal,
                SUM(pg.rank0) as rank0,
                SUM(pg.rank1) as rank1,
                SUM(pg.rank2) as rank2,
                SUM(pg.rank3) as rank3,
                SUM(pg.rank4) as rank4,
                SUM(pg.rank5) as rank5,
                SUM(pg.pointGame) as pointGame,
                SUM(pg.pointChart) as pointChart,
                SUM(pg.pointChartWithoutDlc) as pointChartWithoutDlc,
                SUM(pg.nbChart) as nbChart,
                SUM(pg.nbChartWithoutDlc) as nbChartWithoutDlc,
                SUM(pg.nbChartProven) as nbChartProven,
                SUM(pg.nbChartProvenWithoutDlc) as nbChartProvenWithoutDlc,
                COUNT(DISTINCT ug.idGame) as nbGame
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGame pg
            JOIN pg.game g
            WHERE g.idSerie = :idSerie
            GROUP BY pg.idPlayer
            ORDER BY pointChart DESC");

        $query->setParameter('idSerie', $idSerie);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $list[] = $row;
        }

        $list = Ranking::addRank($list, 'rankPoint', ['pointChart']);
        $list = Ranking::order($list, ['rank0' => SORT_DESC, 'rank1' => SORT_DESC, 'rank2' => SORT_DESC, 'rank3' => SORT_DESC]);
        $list = Ranking::addRank($list, 'rankMedal', ['rank0', 'rank1', 'rank2', 'rank3', 'rank4', 'rank5']);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        $serie = $this->_em->find('VideoGamesRecords\CoreBundle\Entity\Serie', $idSerie);

        foreach ($list as $row) {
            $playerSerie = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\PlayerSerie'
            );
            $playerSerie->setPlayer($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $row['idPlayer']));
            $playerSerie->setSerie($serie);

            $this->_em->persist($playerSerie);
            $this->_em->flush($playerSerie);
        }
    }
}
