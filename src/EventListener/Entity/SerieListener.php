<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs as BaseLifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Contracts\BadgeInterface;
use VideoGamesRecords\CoreBundle\Entity\Badge;
use VideoGamesRecords\CoreBundle\Entity\Serie;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerSerieRankingHandler;
use VideoGamesRecords\CoreBundle\ValueObject\SerieStatus;

class SerieListener
{
    private array $changeSet = array();

    private PlayerSerieRankingHandler $rankingHandler;

    public function __construct(PlayerSerieRankingHandler $rankingHandler)
    {
        $this->rankingHandler = $rankingHandler;
    }

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

    /**
     * @param Serie              $serie
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Serie $serie, PreUpdateEventArgs $event): void
    {
        $this->changeSet = $event->getEntityChangeSet();
    }

    /**
     * @param Serie                  $serie
     * @param BaseLifecycleEventArgs $event
     */
    public function postUpdate(Serie $serie, BaseLifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();

        if (
            array_key_exists('status', $this->changeSet)
            && $this->changeSet['status'][1] == SerieStatus::ACTIVE
        ) {
            $this->rankingHandler->handle($serie->getId());
        }

        $em->flush();
    }
}
