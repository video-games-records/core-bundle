<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\DBAL\Exception;
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
     * @throws Exception
     */
    public function postUpdate(Player $player, LifecycleEventArgs $event): void
    {
        if (array_key_exists('team', $this->changeSet)) {
            $em = $event->getObjectManager();
            $this->updateChartStatusHandler->playerMajStatusTeam($player);
            $em->flush();
        }
    }
}
