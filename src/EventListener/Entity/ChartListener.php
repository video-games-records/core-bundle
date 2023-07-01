<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Chart;

class ChartListener
{
    /**
     * @param Chart       $chart
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Chart $chart, LifecycleEventArgs $event): void
    {
        if (null == $chart->getLibChartFr()) {
            $chart->setLibChartFr($chart->getLibChartEn());
        }
        $chart->getGroup()->setNbChart($chart->getGroup()->getNbChart() + 1);
        $chart->getGroup()->getGame()->setNbChart($chart->getGroup()->getGame()->getNbChart() + 1);
    }


    /**
     * @param Chart       $chart
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Chart $chart, PreUpdateEventArgs $event): void
    {
        if (null == $chart->getLibChartFr()) {
            $chart->setLibChartFr($chart->getLibChartEn());
        }
    }


    /**
     * @param Chart       $chart
     * @param LifecycleEventArgs $event
     */
    public function preRemove(Chart $chart, LifecycleEventArgs $event): void
    {
        $chart->getGroup()->setNbChart($chart->getGroup()->getNbChart() - 1);
        $chart->getGroup()->getGame()->setNbChart($chart->getGroup()->getGame()->getNbChart() - 1);
    }
}
