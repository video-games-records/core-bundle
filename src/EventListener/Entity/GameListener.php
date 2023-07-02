<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use DateTime;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs as BaseLifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use VideoGamesRecords\CoreBundle\Entity\Badge;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\PlayerGame;
use VideoGamesRecords\CoreBundle\Entity\Serie;
use VideoGamesRecords\CoreBundle\Event\GameEvent;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class GameListener
{
    private bool $majPlayers = false;
    private array $changeSet = array();
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Game                   $game
     * @param BaseLifecycleEventArgs $event
     */
    public function prePersist(Game $game, BaseLifecycleEventArgs $event): void
    {
        if (null == $game->getLibGameFr()) {
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
        $this->changeSet = $event->getEntityChangeSet();

        if (array_key_exists('boolRanking', $this->changeSet)) {
            $this->majPlayers = true;
        }

        if ($game->getStatus()->isActive() && ($game->getPublishedAt() == null)) {
            $game->setPublishedAt(new DateTime());
            $event = new GameEvent($game);
            $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::GAME_PUBLISHED);
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

        if (array_key_exists('serie', $this->changeSet)) {
            $this->majSerie($this->changeSet['serie'][0]);
            $this->majSerie($this->changeSet['serie'][1]);
        }

        $em->flush();
    }


    /**
     * @param Serie|null $serie
     * @return void
     */
    private function majSerie(?Serie $serie): void
    {
        if (null === $serie) {
            return;
        }

        $serie->setNbGame(count($serie->getGames()));
        $nbChart = 0;
        foreach ($serie->getGames() as $game) {
            $nbChart += $game->getNbChart();
        }
        $serie->setNbChart($nbChart);
    }
}
