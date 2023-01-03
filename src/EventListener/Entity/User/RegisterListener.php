<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity\User;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use ProjetNormandie\UserBundle\Entity\User;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\PlayerBadge;

class RegisterListener
{
    const GROUP_PLAYER = 2;
    const BADGE_REGISTER = 1;

    public function postPersist(User $user, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();

        // Role Player
        $group = $em->getReference('ProjetNormandie\UserBundle\Entity\Group', self::GROUP_PLAYER);
        $user->addGroup($group);

        // Player
        $player = new Player();
        $player->setId($user->getId());
        $player->setPseudo($user->getUsername());
        $player->setSlug($user->getSlug());
        $player->setUser($user);
        $em->persist($player);

        // Register Badge
        $badge = $em->getReference('VideoGamesRecords\CoreBundle\Entity\Badge', self::BADGE_REGISTER);
        $playerBadge = new PlayerBadge();
        $playerBadge->setPlayer($player);
        $playerBadge->setBadge($badge);
        $em->persist($playerBadge);

        $em->flush();
    }
}