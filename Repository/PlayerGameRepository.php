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
     * Return data from player with game and platforms
     *
     * @param $player
     * @return array
     */
    public function getFromPlayer($player): array
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
