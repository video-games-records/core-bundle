<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Group;

class GroupListener
{

    /**
     * @param Group       $group
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Group $group, LifecycleEventArgs $event)
    {
        if ($group->getLibGroupFr() == null) {
            $group->setLibGroupFr($group->getLibGroupEn());
        }
    }

     /**
     * @param Group       $group
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Group $group, PreUpdateEventArgs $event)
    {
        if ($group->getLibGroupFr() == null) {
            $group->setLibGroupFr($group->getLibGroupEn());
        }
    }
}
