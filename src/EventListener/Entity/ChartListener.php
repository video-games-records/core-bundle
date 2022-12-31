<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Service\Stats\Write\GameStatsHandler;
use VideoGamesRecords\CoreBundle\Service\Stats\Write\GroupStatsHandler;

class ChartListener
{
    public function __construct(
        private readonly GameStatsHandler $gameStatsHandler,
        private readonly GroupStatsHandler $groupStatsHandler
    ) {}

    /**
     * @param Chart       $chart
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Chart $chart, LifecycleEventArgs $event): void
    {
        if (null === $chart->getLibChartFr()) {
            $chart->setLibChartFr($chart->getLibChartEn());
        }
    }


    public function postPersist(Chart $chart, LifecycleEventArgs $event): void
    {
        $this->groupStatsHandler->majNbChart($chart->getGroup());
        $this->gameStatsHandler->majNbChart($chart->getGroup()->getGame());
    }

    /**
     * @param Chart       $chart
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Chart $chart, PreUpdateEventArgs $event): void
    {
        if (null === $chart->getLibChartFr()) {
            $chart->setLibChartFr($chart->getLibChartEn());
        }
    }

    /**
     * @param PlayerChart        $playerChart
     * @param LifecycleEventArgs $event
     * @return void
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function postRemove(PlayerChart $playerChart, LifecycleEventArgs $event): void
    {
        $this->groupStatsHandler->handle($playerChart->getChart()->getGroup());
        $this->gameStatsHandler->handle($playerChart->getChart()->getGroup()->getGame());
    }
}
