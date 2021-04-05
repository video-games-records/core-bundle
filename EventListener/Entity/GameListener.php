<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Game;
use ProjetNormandie\ForumBundle\Service\ForumManager;

class GameListener
{
    private $forumManager;

    /**
     * GameListener constructor.
     * @param ForumManager $forumManager
     */
    public function __construct(ForumManager $forumManager)
    {
        $this->forumManager = $forumManager;
    }

    /**
     * @param Game       $game
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Game $game, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        $forum = $this->forumManager->getForum($game->getDefaultName());
        $game->setForum($forum);
    }
}
