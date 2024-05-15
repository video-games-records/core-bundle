<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\LostPosition;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Event\PlayerChartEvent;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class InsertLostPositionSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::PLAYER_CHART_MAJ_COMPLETED => 'insertLostPosition',
        ];
    }

    /**
     * @param PlayerChartEvent $event
     * @throws ORMException
     */
    public function insertLostPosition(PlayerChartEvent $event): void
    {
        $playerChart = $event->getPlayerChart();
        $oldRank = $event->getOldRank();
        $oldNbEqual = $event->getOldNbEqual();
        $newRank = $playerChart->getRank();
        $newNbEqual = $playerChart->getNbEqual();

        if (
            (($oldRank >= 1) && ($oldRank <= 3) && ($newRank > $oldRank)) ||
            (($oldRank === 1) && ($oldNbEqual === 1) && ($newRank === 1) && ($newNbEqual > 1))
        ) {
            $lostPosition = new LostPosition();
            $lostPosition->setNewRank($newRank);
            $lostPosition->setOldRank(($oldNbEqual == 1 && $oldRank == 1) ? 0 : $oldRank); //----- zero for losing platinum medal
            $lostPosition->setPlayer($this->em->getReference(Player::class, $playerChart->getPlayer()->getId()));
            $lostPosition->setChart($this->em->getReference(Chart::class, $playerChart->getChart()->getId()));
            $this->em->persist($lostPosition);
        }
    }
}
