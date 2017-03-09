<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

/**
 * TeamChartRepository
 */
class TeamChartRepository extends EntityRepository
{


    /**
     * @param $idChart
     * @return array
     */
    public function maj($idChart)
    {
        //----- delete
        $query = $this->_em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\TeamChart tc WHERE tc.idChart = :idChart');
        $query->setParameter('idChart', $idChart);
        $query->execute();

        $query = $this->_em->createQuery("
            SELECT
                 p.idTeam,
                 pc.idPlayer,
                 pc.pointChart
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            WHERE pc.idChart = :idChart
            AND p.idTeam IS NOT NULL
            ORDER BY pc.pointChart DESC");

        $query->setParameter('idChart', $idChart);
        $result = $query->getResult();

        $list = array();
        foreach ($result as $row) {
            $idTeam = $row['idTeam'];
            if (!isset($list[$idTeam])) {
                $list[$idTeam] = [
                    'idTeam' => $idTeam,
                    'nbPlayer' => 1,
                    'pointChart' => $row['pointChart'],
                    'rank0' => 0,
                    'rank1' => 0,
                    'rank2' => 0,
                    'rank3' => 0,
                ];
            } elseif ($list[$idTeam]['nbPlayer'] < 5) {
                $list[$idTeam]['nbPlayer'] = $list[$idTeam]['nbPlayer'] + 1;
                $list[$idTeam]['pointChart'] = $list[$idTeam]['pointChart'] + $row['pointChart'];
            }
        }

        //----- Return teams id
        $teams = array_keys($list);

        //----- add some data
        $list = array_values($list);
        $list = Ranking::order($list, ['pointChart' => 'DESC']);
        $list = Ranking::addRank($list, 'rank', ['pointChart'], true);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        $nbTeam = count($list);

        foreach ($list as $row) {
            //----- add medals
            if ($row['rank'] == 1 && $row['nbEqual'] == 1 && $nbTeam > 1) {
                $row['rank0'] = 1;
                $row['rank1'] = 1;
            } elseif ($row['rank'] == 1 && $row['nbEqual'] == 1 && $nbTeam == 1) {
                $row['rank1'] = 1;
            } elseif ($row['rank'] == 1 && $row['nbEqual'] > 1) {
                $row['rank1'] = 1;
            } elseif ($row['rank'] == 2) {
                $row['rank2'] = 1;
            } elseif ($row['rank'] == 3) {
                $row['rank3'] = 1;
            }

            $teamChart = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\TeamChart'
            );
            $teamChart->setTeam($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $row['idTeam']));
            $teamChart->setChart($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Chart', $idChart));

            $this->_em->persist($teamChart);
        }
        $this->_em->flush();

        return $teams;
    }
}
