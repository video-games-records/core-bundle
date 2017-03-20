<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

/**
 * TeamGroupRepository
 */
class TeamGroupRepository extends EntityRepository
{

    /**
     * @param $idGroup
     * @return array
     */
    public function maj($idGroup)
    {
        //----- delete
        $query = $this->_em->createQuery(
            'DELETE VideoGamesRecords\CoreBundle\Entity\TeamGroup tg WHERE tg.idGroup = :idGroup'
        );
        $query->setParameter('idGroup', $idGroup);
        $query->execute();

        //----- select ans save result in array
        $query = $this->_em->createQuery("
            SELECT
                tc.idTeam,
                (c.idGroup) as idGame,
                '' as rankPointChart,
                '' as rankMedal,
                SUM(tc.chartRank0) as chartRank0,
                SUM(tc.chartRank1) as chartRank1,
                SUM(tc.chartRank2) as chartRank2,
                SUM(tc.chartRank3) as chartRank3,
                SUM(tc.pointChart) as pointChart
            FROM VideoGamesRecords\CoreBundle\Entity\TeamChart tc
            JOIN tc.chart c
            WHERE c.idGroup = :idGroup
            GROUP BY tc.idTeam
            ORDER BY pointChart DESC");


        $query->setParameter('idGroup', $idGroup);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $list[] = $row;
        }

        //----- add some data
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart'], true);
        $list = Ranking::order($list, ['chartRank0' => 'DESC', 'chartRank1' => 'DESC', 'chartRank2' => 'DESC', 'chartRank3' => 'DESC']);
        $list = Ranking::addRank($list, 'rankMedal', ['chartRank0', 'chartRank1', 'chartRank2', 'chartRank3']);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        foreach ($list as $row) {
            $teamGroup = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\TeamGroup'
            );
            $teamGroup->setTeam($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $row['idTeam']));
            $teamGroup->setGroup($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Group', $idGroup));

            $this->_em->persist($teamGroup);
        }
        $this->_em->flush();
    }
}
