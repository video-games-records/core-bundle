<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\Persistence\Event\LifecycleEventArgs as BaseLifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Contracts\BadgeInterface;
use VideoGamesRecords\CoreBundle\Entity\Badge;
use VideoGamesRecords\CoreBundle\Entity\Serie;

class SerieListener
{
    /**
     * @param Serie                   $serie
     * @param BaseLifecycleEventArgs $event
     */
    public function prePersist(Serie $serie, BaseLifecycleEventArgs $event): void
    {
        $badge = new Badge();
        $badge->setType(BadgeInterface::TYPE_SERIE);
        $badge->setPicture('default.gif');
        $serie->setBadge($badge);
    }
}
