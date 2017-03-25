<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Tools\Score;

class PlayerChartLibRepository extends EntityRepository
{
    /**
     * @param \VideoGamesRecords\CoreBundle\Entity\Player $player
     * @param \VideoGamesRecords\CoreBundle\Entity\Chart $chart
     * @return array
     */
    public function getFormValues(Player $player, Chart $chart = null)
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

        if (null !== $chart) {
            $query->andWhere('lib.chart = :chart')
                ->setParameter('chart', $chart);
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
}
