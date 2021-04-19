<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;

class PlayerChartListener
{
    /**
     * @param PlayerChart        $playerChart
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function prePersist(PlayerChart $playerChart, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        $playerChart->setStatus($em->getReference('VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus', 1));
        $playerChart->setLastUpdate(new DateTime());
    }

    /**
     * @param PlayerChart        $playerChart
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PlayerChart $playerChart, PreUpdateEventArgs $event)
    {
        $playerChart->setTopScore(false);
        if ($playerChart->getRank() === 1) {
            $playerChart->setTopScore(true);
        }

        if ($playerChart->getStatus()->getId() === PlayerChartStatus::ID_STATUS_NORMAL) {
            $playerChart->setProof(null);
        }

        if (null === $playerChart->getDateInvestigation() && PlayerChartStatus::ID_STATUS_INVESTIGATION === $playerChart->getStatus()->getId()) {
            $playerChart->setDateInvestigation(new DateTime());
        }
        if (null !== $playerChart->getDateInvestigation()
            && in_array($playerChart->getStatus()->getId(), [PlayerChartStatus::ID_STATUS_PROOVED, PlayerChartStatus::ID_STATUS_NOT_PROOVED], true)) {
            $playerChart->setDateInvestigation(null);
        }

        //-- status
        if ($playerChart->getStatus()->getId() == PlayerChartStatus::ID_STATUS_NOT_PROOVED) {
            $playerChart->setPointChart(0);
            $playerChart->setRank(0);
            $playerChart->setTopScore(false);
        }
    }
}
