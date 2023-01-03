<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity\User;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use ProjetNormandie\UserBundle\Entity\User;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\PlayerBadge;

class UpdateListener
{
    public function postUpdate(User $user, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();

        /** @var Player $player */
        $player = $em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')->find($user->getId());
        $player->setPseudo($user->getUsername());
        $player->setSlug($user->getSlug());
        $player->setAvatar($user->getAvatar());

        $em->flush();
    }
}
