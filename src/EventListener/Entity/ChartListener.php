<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
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

    /**
     * @param Chart       $chart
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Chart $chart, PreUpdateEventArgs $event)
    {
        if ($chart->getLibChartFr() == null) {
            $chart->setLibChartFr($chart->getLibChartEn());
        }
    }
}
