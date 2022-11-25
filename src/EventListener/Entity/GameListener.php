<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use ProjetNormandie\ForumBundle\Manager\ForumManager;
use VideoGamesRecords\CoreBundle\Entity\Badge;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\ValueObject\GameStatus;

class GameListener
{
    private ForumManager $forumManager;
    private bool $majPlayers = false;

    /**
     * GameListener constructor.
     * @param ForumManager  $forumManager
     */
    public function __construct(ForumManager $forumManager)
    {
        $this->forumManager = $forumManager;
    }

    /**
     * @param Game               $game
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function prePersist(Game $game, LifecycleEventArgs $event)
    {
        if ($game->getLibGameFr() == null) {
            $game->setLibGameFr($game->getLibGameEn());
        }
        $forum = $this->forumManager->getForum([
            'libForum' => $game->getLibGameEn(),
            'libForumFr' => $game->getLibGameFr(),
            'parent' => 10953
        ]);
        $game->setForum($forum);

        $badge = new Badge();
        $badge->setType('Master');
        $badge->setPicture('master_default.gif');
        $game->setBadge($badge);
    }

    /**
     * @param Game       $game
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Game $game, PreUpdateEventArgs $event)
    {
        $changeSet = $event->getEntityChangeSet();

        if (array_key_exists('boolRanking', $changeSet)) {
            $this->majPlayers = true;
        }

        if ($game->getStatus()->isActive() && ($game->getPublishedAt() == null)) {
            $game->setPublishedAt(new DateTime());
        }

        if (array_key_exists('status', $changeSet)) {
            if (($changeSet['status'][0] == GameStatus::STATUS_INACTIVE) && ($changeSet['status'][1] == GameStatus::STATUS_ACTIVE)) {
                $game->setEtat(Game::ETAT_END);
            }
        }

        if (array_key_exists('picture', $changeSet)) {
            $game->setEtat(Game::ETAT_PICTURE);
        }
    }

    /**
     * @param Game               $game
     * @param LifecycleEventArgs $event
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postUpdate(Game $game, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        if ($this->majPlayers) {
            foreach ($game->getPlayerGame() as $playerGame) {
                $playerGame->getPlayer()->setBoolMaj(true);
            }
        }
        $em->flush();
    }
}
