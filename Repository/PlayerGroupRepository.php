<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;

class PlayerGroupRepository extends EntityRepository
{
    /**
     * @param array $params idGroup|idPlayer|limit|maxRank
     * @return array
     */
    public function getRankingPoints($params = [])
    {
        $query = $this->createQueryBuilder('pg')
            ->join('pg.player', 'p')
            ->addSelect('p')//----- for using ->getPlayer() on each result
            ->orderBy('pg.rankPoint');

        $query->where('pg.idGroup = :idGroup')
            ->setParameter('idGroup', $params['idGroup']);

        if (array_key_exists('limit', $params)) {
            $query->setMaxResults($params['limit']);
        }

        if ((array_key_exists('maxRank', $params)) && (array_key_exists('idPlayer', $params))) {
            $query->andWhere('(pg.rankPoint <= :maxRank OR pg.idPlayer = :idPlayer)')
                ->setParameter('maxRank', $params['maxRank'])
                ->setParameter('idPlayer', $params['idPlayer']);
        } elseif (array_key_exists('maxRank', $params)) {
            $query->andWhere('pg.rankPoint <= :maxRank')
                ->setParameter('maxRank', $params['maxRank']);
        }

        return $query->getQuery()->getResult();
    }


    /**
     * @param array $params idGroup|idPlayer|limit|maxRank
     * @return array
     */
    public function getRankingMedals($params = [])
    {
        $query = $this->createQueryBuilder('pg')
            ->join('pg.player', 'p')
            ->addSelect('p')//----- for using ->getPlayer() on each result
            ->orderBy('pg.rankMedal');

        $query->where('pg.idGroup = :idGroup')
            ->setParameter('idGroup', $params['idGroup']);

        if (array_key_exists('limit', $params)) {
            $query->setMaxResults($params['limit']);
        }

        if ((array_key_exists('maxRank', $params)) && (array_key_exists('idPlayer', $params))) {
            $query->andWhere('(pg.rankMedal <= :maxRank OR pg.idPlayer = :idPlayer)')
                ->setParameter('maxRank', $params['maxRank'])
                ->setParameter('idPlayer', $params['idPlayer']);
        } elseif (array_key_exists('maxRank', $params)) {
            $query->andWhere('pg.rankMedal <= :maxRank')
                ->setParameter('maxRank', $params['maxRank']);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param int $idGroup
     */
    public function maj($idGroup)
    {
        //----- delete
        $query = $this->_em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\PlayerGroup pg WHERE pg.idGroup = :idGroup');
        $query->setParameter('idGroup', $idGroup);
        $query->execute();

        $data = [];

        //----- data rank0
        $query = $this->_em->createQuery("
            SELECT
                 pc.idPlayer,
                 COUNT(pc.idChart) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.chart c
            WHERE c.idGroup = :idGroup
            AND pc.rank = 1
            AND c.nbPost > 0
            AND pc.nbEqual = 1
            GROUP BY pc.idPlayer");


        $query->setParameter('idGroup', $idGroup);
        $result = $query->getResult();
        foreach ($result as $row) {
            $data['rank0'][$row['idPlayer']] = $row['nb'];
        }

        //----- data rank1 to rank5
        $query = $this->_em->createQuery("
            SELECT
                 pc.idPlayer,
                 COUNT(pc.idChart) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.chart c
            WHERE c.idGroup = :idGroup
            AND pc.rank = :rank
            GROUP BY pc.idPlayer");
        $query->setParameter('idGroup', $idGroup);

        for ($i = 1; $i <= 5; $i++) {
            $query->setParameter('rank', $i);
            $result = $query->getResult();
            foreach ($result as $row) {
                $data["rank$i"][$row['idPlayer']] = $row['nb'];
            }
        }

        //----- data nbRecordProuve
        $query = $this->_em->createQuery("
            SELECT
                 pc.idPlayer,
                 COUNT(pc.idChart) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.chart c
            WHERE c.idGroup = :idGroup
            AND pc.idStatus = :idStatus
            GROUP BY pc.idPlayer");

        $query->setParameter('idGroup', $idGroup);
        $query->setParameter('idStatus', PlayerChartStatus::ID_STATUS_PROOVED);

        $result = $query->getResult();
        foreach ($result as $row) {
            $data['nbChartProven'][$row['idPlayer']] = $row['nb'];
        }


        //----- select ans save result in array
        $query = $this->_em->createQuery("
            SELECT
                pc.idPlayer,
                (c.idGroup) as idGroup,
                '' as rankPoint,
                '' as rankMedal,
                SUM(pc.pointChart) as pointChart,
                COUNT(pc.idChart) as nbChart
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.chart c
            WHERE c.idGroup = :idGroup
            GROUP BY pc.idPlayer
            ORDER BY pointChart DESC");


        $query->setParameter('idGroup', $idGroup);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $row['rankMedal'] = 0;
            $row['rank0'] = (isset($data['rank0'][$row['idPlayer']])) ? $data['rank0'][$row['idPlayer']] : 0;
            $row['rank1'] = (isset($data['rank1'][$row['idPlayer']])) ? $data['rank1'][$row['idPlayer']] : 0;
            $row['rank2'] = (isset($data['rank2'][$row['idPlayer']])) ? $data['rank2'][$row['idPlayer']] : 0;
            $row['rank3'] = (isset($data['rank3'][$row['idPlayer']])) ? $data['rank3'][$row['idPlayer']] : 0;
            $row['rank4'] = (isset($data['rank4'][$row['idPlayer']])) ? $data['rank4'][$row['idPlayer']] : 0;
            $row['rank5'] = (isset($data['rank5'][$row['idPlayer']])) ? $data['rank5'][$row['idPlayer']] : 0;
            $row['nbChartProven'] = (isset($data['nbChartProven'][$row['idPlayer']])) ? $data['nbChartProven'][$row['idPlayer']] : 0;
            $list[] = $row;
        }

        //----- add some data
        $list = Ranking::addRank($list, 'rankPoint', ['pointChart']);
        $list = Ranking::order($list, ['rank0' => 'DESC', 'rank1' => 'DESC', 'rank2' => 'DESC', 'rank3' => 'DESC']);
        $list = Ranking::addRank($list, 'rankMedal', ['rank0', 'rank1', 'rank2', 'rank3', 'rank4', 'rank5']);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        $group = $this->_em->find('VideoGamesRecords\CoreBundle\Entity\Group', $idGroup);

        foreach ($list as $row) {
            $playerGroup = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\PlayerGroup'
            );
            $playerGroup->setPlayer($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $row['idPlayer']));
            $playerGroup->setGroup($group);

            $this->_em->persist($playerGroup);
        }
        $this->_em->flush();
    }
}
