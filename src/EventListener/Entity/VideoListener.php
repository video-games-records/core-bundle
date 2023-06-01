<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Video;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

class VideoListener
{
     private UserProvider $userProvider;

    /**
     * @param UserProvider $userProvider
     */
    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param Video     $video
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Video $video, LifecycleEventArgs $event): void
    {
        $video->setPlayer($this->userProvider->getPlayer());
    }
}
