<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;

class ScoreManager
{
    private EntityManagerInterface $em;

    public function __construct(EventDispatcherInterface $eventDispatcher, EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @param Player $player
     * @param int $idGame
     * @param int $idPlatform
     * @return void
     * @throws ORMException
     */
    public function updatePlatform(Player $player, int $idGame, int $idPlatform): void
    {
        $qb = $this->em->createQueryBuilder();
        $query = $qb->update('VideoGamesRecords\CoreBundle\Entity\PlayerChart', 'pc')
            ->set('pc.platform', ':platform')
            ->where('pc.player = :player')
            ->setParameter('platform', $idPlatform)
            ->setParameter('player', $player)
            ->andWhere('pc.chart IN (
                            SELECT c FROM VideoGamesRecords\CoreBundle\Entity\Chart c
                            join c.group g
                        WHERE g.game = :game)')
            ->setParameter('game', $idGame);
        $query->getQuery()->execute();
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
        $query = $qb->select('pc')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerChart', 'pc')
            ->innerJoin('pc.chart', 'c')
            ->innerJoin('c.group', 'g')
            ->where('pc.player = :player')
            ->setParameter('player', $player)
            ->andWhere('g.game = :game')
            ->setParameter('game', $game)
            ->andWhere('pc.platform IS NOT NULL')
            ->orderBy("pc.lastUpdate", "DESC")
            ->setMaxResults(1);

        $playerChart = $query->getQuery()->getOneOrNullResult();

        return $playerChart?->getPlatform();
    }


    /**
     * @param Game $game
     * @param Player $player
     * @param PlayerChart|null $playerChart
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function hasScoreOnGame(Game $game, Player $player, ?PlayerChart $playerChart = null): bool
    {
        $query = $this->em->createQuery("
            SELECT COUNT(pc.chart)
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.chart c
            JOIN c.group g
            WHERE g.game = :game
            AND pc.player = :player");
        $query->setParameter('game', $game);
        $query->setParameter('player', $player);

        return $query->getSingleScalarResult() > 0;
    }


    /**
     * @param Group $group
     * @param Player $player
     * @param PlayerChart|null $playerChart
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function hasScoreOnGroup(Group $group, Player $player, ?PlayerChart $playerChart = null): bool
    {
        $query = $this->em->createQuery(
            "
            SELECT COUNT(pc.chart)
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.chart c
            WHERE c.group = :group
            AND pc.player = :player"
        );
        $query->setParameter('group', $group);
        $query->setParameter('player', $player);

        return $query->getSingleScalarResult() > 0;
    }
}
