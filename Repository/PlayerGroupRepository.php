<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;

class PlayerGroupRepository extends EntityRepository
{
    /**
     * @param int $idGroup
     * @param int $maxRank
     * @param int $idPlayer
     * @return array
     */
    public function getRankingPoints($idGroup, $maxRank = null, $idPlayer = null)
    {
        $query = $this->createQueryBuilder('pg')
            ->join('pg.player', 'p')
            ->join('pg.group', 'g')
            ->addSelect('p')//----- for using ->getPlayer() on each result
            ->orderBy('pg.rankPointChart');

        $query->where('g.id = :idGroup')
            ->setParameter('idGroup', $idGroup);

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
     * @param int $idGroup
     * @param int $maxRank
     * @param int $idPlayer
     * @return array
     */
    public function getRankingMedals($idGroup, $maxRank = null, $idPlayer = null)
    {
        $query = $this->createQueryBuilder('pg')
            ->join('pg.player', 'p')
            ->join('pg.group', 'g')
            ->addSelect('p')//----- for using ->getPlayer() on each result
            ->orderBy('pg.rankMedal');

        $query->where('g.id = :idGroup')
            ->setParameter('idGroup', $idGroup);

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
     * @param Group $group
     */
    public function maj($group)
    {
        //----- delete
        $query = $this->_em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\PlayerGroup pg WHERE pg.group = :group');
        $query->setParameter('group', $group);
        $query->execute();

        $data = [];

        //----- data rank0
        $query = $this->_em->createQuery("
            SELECT
                 p.idPlayer,
                 COUNT(pc.idPlayerChart) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            JOIN pc.chart c
            WHERE c.group = :group
            AND pc.rank = 1
            AND c.nbPost > 0
            AND pc.nbEqual = 1
            GROUP BY p.idPlayer");


        $query->setParameter('group', $group);
        $result = $query->getResult();
        foreach ($result as $row) {
            $data['chartRank0'][$row['idPlayer']] = $row['nb'];
        }

        //----- data rank1 to rank5
        $query = $this->_em->createQuery("
            SELECT
                 p.idPlayer,
                 COUNT(pc.idPlayerChart) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            JOIN pc.chart c
            WHERE c.group = :group
            AND pc.rank = :rank
            GROUP BY p.idPlayer");
        $query->setParameter('group', $group);

        for ($i = 1; $i <= 5; $i++) {
            $query->setParameter('rank', $i);
            $result = $query->getResult();
            foreach ($result as $row) {
                $data["chartRank$i"][$row['idPlayer']] = $row['nb'];
            }
        }

        //----- data nbRecordProuve
        $query = $this->_em->createQuery("
            SELECT
                 p.idPlayer,
                 COUNT(pc.idPlayerChart) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            JOIN pc.chart c
            WHERE c.group = :group
            AND pc.status = :status
            GROUP BY p.idPlayer");

        $query->setParameter('group', $group);
        $query->setParameter(
            'status',
            $this->_em->getReference(
                PlayerChartStatus::class,
                PlayerChartStatus::ID_STATUS_PROOVED
            )
        );

        $result = $query->getResult();
        foreach ($result as $row) {
            $data['nbChartProven'][$row['idPlayer']] = $row['nb'];
        }


        //----- select ans save result in array
        $query = $this->_em->createQuery("
            SELECT
                p.idPlayer,
                '' as rankPoint,
                '' as rankMedal,
                SUM(pc.pointChart) as pointChart,
                COUNT(pc.idPlayerChart) as nbChart
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            JOIN pc.chart c
            WHERE c.group = :group
            GROUP BY p.idPlayer
            ORDER BY pointChart DESC");


        $query->setParameter('group', $group);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $row['rankMedal'] = 0;
            $row['chartRank0'] = (isset($data['chartRank0'][$row['idPlayer']])) ? $data['chartRank0'][$row['idPlayer']] : 0;
            $row['chartRank1'] = (isset($data['chartRank1'][$row['idPlayer']])) ? $data['chartRank1'][$row['idPlayer']] : 0;
            $row['chartRank2'] = (isset($data['chartRank2'][$row['idPlayer']])) ? $data['chartRank2'][$row['idPlayer']] : 0;
            $row['chartRank3'] = (isset($data['chartRank3'][$row['idPlayer']])) ? $data['chartRank3'][$row['idPlayer']] : 0;
            $row['chartRank4'] = (isset($data['chartRank4'][$row['idPlayer']])) ? $data['chartRank4'][$row['idPlayer']] : 0;
            $row['chartRank5'] = (isset($data['chartRank5'][$row['idPlayer']])) ? $data['chartRank5'][$row['idPlayer']] : 0;
            $row['nbChartProven'] = (isset($data['nbChartProven'][$row['idPlayer']])) ? $data['nbChartProven'][$row['idPlayer']] : 0;
            $list[] = $row;
        }

        //----- add some data
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart']);
        $list = Ranking::order($list, ['chartRank0' => SORT_DESC, 'chartRank1' => SORT_DESC, 'chartRank2' => SORT_DESC, 'chartRank3' => SORT_DESC]);
        $list = Ranking::addRank($list, 'rankMedal', ['chartRank0', 'chartRank1', 'chartRank2', 'chartRank3', 'chartRank4', 'chartRank5']);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

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
