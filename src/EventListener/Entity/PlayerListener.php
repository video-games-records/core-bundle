<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\ValueObject\ChartStatus;

class PlayerListener
{
    private array $changeSet = array();

    /**
     * @param Player             $player
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Player $player, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();
        $player->setStatus($em->getReference('VideoGamesRecords\CoreBundle\Entity\PlayerStatus', 1));
    }

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
            $conn = $em->getConnection();
            $sql = 'UPDATE vgr_chart
                SET statusTeam = :status
                WHERE id IN (SELECT idChart FROM vgr_player_chart WHERE idPlayer = :idPlayer)';
            $stmt = $conn->prepare($sql);
            $stmt->executeQuery(['status' => ChartStatus::STATUS_MAJ, 'idPlayer' => $player->getId()]);
            $em->flush();
        }
    }
}
