<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\PlayerGame;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use DateTime;
use Symfony\Component\Intl\Locale;

class PlayerGameRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerGame::class);
    }

    /**
     * @param Game $game
     * @param null $maxRank
     * @param null $player
     * @param null $team
     * @return PlayerGame[]
     */
    public function getRankingPoints(Game $game, $maxRank = null, $player = null, $team = null)
    {
        $query = $this->createQueryBuilder('pg')
            ->join('pg.player', 'p')
            ->addSelect('p')
            ->orderBy('pg.rankPointChart');

        $query->where('pg.game = :game')
            ->setParameter('game', $game);

        if ($team != null) {
            $query->andWhere('(p.team = :team)')
                ->setParameter('team', $team);
        } elseif (($maxRank !== null) && ($player !== null)) {
            $query->andWhere('(pg.rankPointChart <= :maxRank OR pg.player = :player)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('player', $player);
        } elseif ($maxRank !== null) {
            $query->andWhere('pg.rankPointChart <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @param Game $game
     * @param null $maxRank
     * @param null $player
     * @return array
     */
    public function getRankingMedals(Game $game, $maxRank = null, $player = null)
    {
        $query = $this->createQueryBuilder('pg')
            ->join('pg.player', 'p')
            ->addSelect('p')
            ->orderBy('pg.rankMedal');

        $query->where('pg.game = :game')
            ->setParameter('game', $game);

        if (($maxRank !== null) && ($player !== null)) {
            $query->andWhere('(pg.rankMedal <= :maxRank OR pg.player = :player)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('player', $player);
        } elseif ($maxRank !== null) {
            $query->andWhere('pg.rankMedal <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Return data from player with game and platforms
     *
     * @param $player
     * @return int|mixed|string
     */
    public function getFromPlayer($player)
    {
        $qb = $this->createQueryBuilder('pg')
            ->join('pg.game', 'g')
            ->addSelect('g')
            ->join('g.platforms', 'p')
            ->addSelect('p')
            ->where('pg.player = :player')
            ->setParameter('player', $player)
            ->orderBy('g.' . (Locale::getDefault() == 'fr' ? 'libGameFr' : 'libGameEn'), 'ASC');

         return $qb->getQuery()->getResult();
    }
}
