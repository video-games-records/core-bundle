<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
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
}
