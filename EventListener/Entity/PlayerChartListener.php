<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\LostPosition;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;

class PlayerChartListener
{
    private $lostPosition;

    /**
     * @param PlayerChart        $playerChart
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PlayerChart $playerChart, PreUpdateEventArgs $event)
    {
        $em = $event->getEntityManager();
        $changeSet = $event->getEntityChangeSet();

        //-- status
        if ($playerChart->getStatus()->getId() == PlayerChartStatus::ID_STATUS_NOT_PROOVED) {
            $playerChart->setPointChart(0);
            $playerChart->setRank(0);
            $playerChart->setTopScore(0);
        }

        //----- LostPosition
        $oldRank = $playerChart->getRank();
        $newRank = $oldRank;
        $oldNbEqual = $playerChart->getNbEqual();
        $newNbEqual = $oldNbEqual;

        if (array_key_exists('rank', $changeSet)) {
            $oldRank = $changeSet['rank'][0];
            $newRank = $changeSet['rank'][1];
        }
        if (array_key_exists('nbEqual', $changeSet)) {
            $oldNbEqual = $changeSet['nbEqual'][0];
            $newNbEqual = $changeSet['nbEqual'][1];
        }

        if ((($oldRank >= 1) && ($oldRank <= 3) && ($newRank > $oldRank)) ||
            (($oldRank === 1) && ($oldNbEqual === 1) && ($newRank === 1) && ($newNbEqual > 1))
        ) {
            try {
                $this->lostPosition = new LostPosition();
                $this->lostPosition ->setNewRank($newRank);
                $this->lostPosition ->setOldRank(($newRank === 1) ? 0 : $oldRank); //----- zero for losing platinum medal
                $this->lostPosition ->setPlayer($em->getReference(Player::class, $playerChart->getPlayer()->getId()));
                $this->lostPosition ->setChart($em->getReference(Chart::class, $playerChart->getChart()->getId()));
            } catch (ORMException $e) {
            }
        }
    }

    /**
     * @param PlayerChart        $playerChart
     * @param LifecycleEventArgs $event
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postUpdate(PlayerChart $playerChart, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        if ($this->lostPosition !== null) {
            $em->persist($this->lostPosition);
            $em->flush($this->lostPosition);
            $this->lostPosition = null;
        }
    }
}
