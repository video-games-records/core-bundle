<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Event\GameEvent;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class ScorePlatformManager
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher, EntityManagerInterface $em)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->em = $em;
    }


    /**
     * @param Player   $player
     * @param Game     $game
     * @param Platform $platform
     * @return void
     */
    public function updatePlatform(Player $player, Game $game, Platform $platform): void
    {
        $qb = $this->em->createQueryBuilder();
        $query = $qb->update('VideoGamesRecords\CoreBundle\Entity\PlayerChart', 'pc')
            ->set('pc.platform', ':platform')
            ->where('pc.player = :player')
            ->setParameter('platform', $platform)
            ->setParameter('player', $player)
            ->andWhere('pc.chart IN (
                            SELECT c FROM VideoGamesRecords\CoreBundle\Entity\Chart c
                            join c.group g
                        WHERE g.game = :game)')
            ->setParameter('game', $game);
        //@todo MAJ statut chart to MAJ
        $query->getQuery()->execute();

        $event = new GameEvent($game);
        $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::SCORE_PLATFORM_UPDATED);
    }

    /**
     * @param Player $player
     * @param Game   $game
     * @return Platform|null
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function getPlatform(Player $player, Game $game): ?Platform
    {
        $qb = $this->em->createQueryBuilder();
        $query = $qb->select('(pc.platform) as platform')
            ->distinct()
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerChart', 'pc')
            ->innerJoin('pc.chart', 'c')
            ->innerJoin('c.group', 'g')
            ->where('pc.player = :player')
            ->setParameter('player', $player)
            ->andWhere('g.game = :game')
            ->setParameter('game', $game)
            ->andWhere('pc.platform IS NOT NULL');
        $result = $query->getQuery()->getOneOrNullResult();
        if (null !== $result) {
            return $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Platform', $result['platform']);
        }
        return null;
    }
}
