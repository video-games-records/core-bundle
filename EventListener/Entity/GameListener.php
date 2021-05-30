<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
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
        if ($game->getLibGameFr() == null) {
            $game->setLibGameFr($game->getLibGameEn());
        }
        $forum = $this->forumManager->getForum([
            'libForum' => $game->getLibGameEn(),
            'libForumFr' => $game->getLibGameFr(),
        ]);
        $game->setForum($forum);
    }

    /**
     * @param Game       $game
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Game $game, PreUpdateEventArgs $event)
    {
        if (($game->getStatus() == Game::STATUS_ACTIVE) && ($game->getPublishedAt() == null)) {
            $game->setPublishedAt(new DateTime());
        }
    }
}
