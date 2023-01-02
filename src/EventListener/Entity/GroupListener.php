<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Group;

class GroupListener
{

    /**
     * @param Group       $group
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Group $group, LifecycleEventArgs $event): void
    {
        if (null === $group->getLibGroupFr()) {
            $group->setLibGroupFr($group->getLibGroupEn());
        }

        if ($group->getBoolDlc()) {
            $group->getGame()->setBoolDlc(true);
        }
    }

    /**
     * @param Group       $group
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Group $group, PreUpdateEventArgs $event): void
    {
        if (null === $group->getLibGroupFr()) {
            $group->setLibGroupFr($group->getLibGroupEn());
        }

        $game = $group->getGame();

        /** @var Group $row */
        $game->setBoolDlc(false);
        foreach ($game->getGroups() as $row) {
            if ($row->getBoolDlc()) {
                $game->setBoolDlc(true);
                break;
            }
        }
    }

    /**
     * @param Group       $group
     * @param LifecycleEventArgs $event
     */
    public function preRemove(Group $group, LifecycleEventArgs $event): void
    {
        $game = $group->getGame();
        /** @var Group $row */
        $game->setBoolDlc(false);
        foreach ($game->getGroups() as $row) {
            if ($row->getBoolDlc() && $row->getId() !== $group->getId()) {
                $game->setBoolDlc(true);
                break;
            }
        }
    }
}
