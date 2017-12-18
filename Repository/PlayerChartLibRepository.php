<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Tools\Score;

class PlayerChartLibRepository extends EntityRepository
{
    /**
     * @param \VideoGamesRecords\CoreBundle\Entity\Player $player
     * @param \VideoGamesRecords\CoreBundle\Entity\Chart $chart
     * @param \VideoGamesRecords\CoreBundle\Entity\Group $group
     * @return array
     */
    public function getFormValues(Player $player, Chart $chart = null, Group $group = null)
    {
        $query = $this->createQueryBuilder('pcl')
            ->join('pcl.libChart', 'lib')
            ->addSelect('lib')
            ->join('lib.type', 'type')
            ->addSelect('type')
            ->orderBy('lib.chart')
            ->addOrderBy('pcl.libChart');

        $query->where('pcl.player = :player')
            ->setParameter('player', $player);

        //----- Add filer chart or group
        if (null !== $chart) {
            $query->andWhere('lib.chart = :chart')
                ->setParameter('chart', $chart);
        } elseif (null !== $group) {
            $query->join('lib.chart', 'c')
                ->andWhere('c.group = :group')
                ->setParameter('group', $group);
        }

        $result = $query->getQuery()->getResult();
        $data = [];

        foreach ($result as $row) {
            /** @var \VideoGamesRecords\CoreBundle\Entity\PlayerChartLib $row */
            $data['player_' . $row->getLibChart()->getChart()->getId() . '_' . $row->getLibChart()->getIdLibChart()] = $row->getValue();
            $values = Score::getValues($row->getLibChart()->getType()->getMask(), $row->getValue());
            $i = 1;
            foreach ($values as $key => $value) {
                $data['value_' . $row->getLibChart()->getChart()->getId() . '_' . $row->getLibChart()->getIdLibChart() . '_' . $i++] = $value['value'];
            }
        }

        return $data;
    }

    /**
     * @param \VideoGamesRecords\CoreBundle\Entity\Group $group
     * @return array
     */
    public function getTopValues($group)
    {
        $query = $this->createQueryBuilder('pcl')
            ->join('pcl.libChart', 'lib')
            ->addSelect('lib')
            ->join('lib.type', 'type')
            ->addSelect('type')
            ->join('lib.chart', 'c')
            ->addSelect('c.id')
            ->join('c.playerCharts', 'pc')
            ->join('pc.player', 'p')
            ->addSelect('p.pseudo')
            ->orderBy('lib.idLibChart');

        $query->where('c.idGroup = :idGroup')
            ->setParameter('idGroup', $group->getId());


        $query->andWhere('pc.isTopScore = 1')
            ->andWhere('pc.player = pcl.player');

        $result = $query->getQuery()->getArrayResult();
        $list = array();
        foreach ($result as $row) {
            if (!array_key_exists($row['id'], $list)) {
                $list[$row['id']] = array(
                    'idPlayer' => $row[0]['idPlayer'],
                    'pseudo' => $row['pseudo'],
                    'values' => array(),
                );
            }
            $list[$row['id']]['values'][] = Score::formatScore($row[0]['value'], $row[0]['libChart']['type']['mask']);
        }
        return $list;
    }
}
