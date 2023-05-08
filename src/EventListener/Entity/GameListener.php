<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use DateTime;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\Event\LifecycleEventArgs as BaseLifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Badge;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\PlayerGame;
use VideoGamesRecords\CoreBundle\Service\Stats\Write\SerieStatsHandler;

class GameListener
{
    private bool $majPlayers = false;
    private SerieStatsHandler $serieStatsHandler;

    /**
     * @param SerieStatsHandler $serieStatsHandler
     */
    public function __construct(SerieStatsHandler $serieStatsHandler)
    {
        $this->serieStatsHandler = $serieStatsHandler;
    }

    /**
     * @param Game                   $game
     * @param BaseLifecycleEventArgs $event
     */
    public function prePersist(Game $game, BaseLifecycleEventArgs $event): void
    {
        if (null === $game->getLibGameFr()) {
            $game->setLibGameFr($game->getLibGameEn());
        }

        $badge = new Badge();
        $badge->setType('Master');
        $badge->setPicture('master_default.gif');
        $game->setBadge($badge);
    }

    /**
     * @param Game               $game
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Game $game, PreUpdateEventArgs $event): void
    {
        $changeSet = $event->getEntityChangeSet();

        if (array_key_exists('boolRanking', $changeSet)) {
            $this->majPlayers = true;
        }

        if ($game->getStatus()->isActive() && ($game->getPublishedAt() == null)) {
            $game->setPublishedAt(new DateTime());
        }
    }

    /**
     * @param Game                   $game
     * @param BaseLifecycleEventArgs $event
     */
    public function postUpdate(Game $game, BaseLifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();
        if ($this->majPlayers) {
            $playerGames = $em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerGame')->findBy(['game' => $game]);
            /** @var PlayerGame $playerGame */
            foreach ($playerGames as $playerGame) {
                $playerGame->getPlayer()->setBoolMaj(true);
            }
        }
        $em->flush();

        if (null !== $game->getSerie()) {
            $this->serieStatsHandler->handle($game->getSerie());
        }
    }
}
