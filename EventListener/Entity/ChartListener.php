<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Chart;


class ChartListener
{

    /**
     * @param Chart       $chart
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Chart $chart, LifecycleEventArgs $event)
    {
        if ($chart->getLibChartFr() == null) {
            $chart->setLibChartFr($chart->getLibChartEn());
        }
    }
}
