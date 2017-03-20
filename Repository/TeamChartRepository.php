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
                    'chartRank0' => 0,
                    'chartRank1' => 0,
                    'chartRank2' => 0,
                    'chartRank3' => 0,
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
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart'], true);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        $nbTeam = count($list);

        foreach ($list as $row) {
            //----- add medals
            if ($row['rankPointChart'] == 1 && $row['nbEqual'] == 1 && $nbTeam > 1) {
                $row['chartRank0'] = 1;
                $row['chartRank1'] = 1;
            } elseif ($row['rankPointChart'] == 1 && $row['nbEqual'] == 1 && $nbTeam == 1) {
                $row['chartRank1'] = 1;
            } elseif ($row['rankPointChart'] == 1 && $row['nbEqual'] > 1) {
                $row['chartRank1'] = 1;
            } elseif ($row['rankPointChart'] == 2) {
                $row['chartRank2'] = 1;
            } elseif ($row['rankPointChart'] == 3) {
                $row['chartRank3'] = 1;
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
