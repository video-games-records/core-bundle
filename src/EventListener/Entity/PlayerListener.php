<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Service\UpdateChartStatusHandler;

class PlayerListener
{
    private array $changeSet = array();

    public function __construct(
        private readonly UpdateChartStatusHandler $updateChartStatusHandler
     ) {}


     /**
     * @param Player $player
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Player $player, PreUpdateEventArgs $event): void
    {
        $this->changeSet = $event->getEntityChangeSet();
    }

    /**
     * @param Player             $player
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function postUpdate(Player $player, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();

        if (array_key_exists('team', $this->changeSet)) {
            $this->updateChartStatusHandler->playerMajStatusTeam($player);
        }

        $em->flush();
    }

}
