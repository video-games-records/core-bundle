<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\LostPosition;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Chart;

class PlayerChartUpdateListener
{
    private $lostPosition;

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->lostPosition = null;

        // False check is compulsory otherwise duplication occurs
        if ($entity instanceof PlayerChart) {
            $em = $args->getEntityManager();

            //----- LostPosition
            $oldRank = $entity->getRank();
            $newRank = $entity->getRank();
            $oldNbEqual = $entity->getNbEqual();
            $newNbEqual = $entity->getNbEqual();

            $changeSet = $args->getEntityChangeSet();

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
                $this->lostPosition = new LostPosition();
                $this->lostPosition->setNewRank($newRank);
                $this->lostPosition->setOldRank(($newRank === 1) ? 0 : $oldRank); //----- zero for losing platinum medal
                $this->lostPosition->setPlayer($em->getReference(Player::class, $entity->getPlayer()->getIdPlayer()));
                $this->lostPosition->setChart($em->getReference(Chart::class, $entity->getChart()->getId()));
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        if ($this->lostPosition !== null) {
            $em->persist($this->lostPosition);
            $em->flush($this->lostPosition);
        }
    }
}
