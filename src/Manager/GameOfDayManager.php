<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;

class GameOfDayManager
{
    private EntityManagerInterface $em;
    private CacheInterface $cache;

    public function __construct(EntityManagerInterface $em, CacheInterface $cache)
    {
        $this->em = $em;
        $this->cache = $cache;
    }


    public function getGameOfDay(): ?Game
    {
        $today = (new \DateTime())->format('Y-m-d');

        return $this->cache->get("game_of_day_{$today}", function (ItemInterface $item) {
            // Cache expire à minuit (début du jour suivant)
            $tomorrow = new \DateTime('tomorrow');
            $item->expiresAt($tomorrow);

            // Récupérer un jeu aléatoire parmi les jeux actifs
            return $this->selectRandomGame();
        });
    }

    /**
     * Force la régénération du jeu du jour (vide le cache)
     */
    public function regenerateGameOfDay(): ?Game
    {
        $today = (new \DateTime())->format('Y-m-d');

        // Supprimer du cache
        $this->cache->delete("game_of_day_{$today}");

        // Récupérer un nouveau jeu
        return $this->getGameOfDay();
    }


    private function selectRandomGame(): ?Game
    {
        // Récupérer tous les jeux actifs avec des charts
        $games = $this->em->getRepository(Game::class)
            ->createQueryBuilder('g')
            ->where('g.status = :active')
            ->andWhere('g.isRank = true')
            ->andWhere('g.nbChart > 0')
            ->setParameter('active', 'ACTIVE')
            ->getQuery()
            ->getResult();

        if (empty($games)) {
            return null;
        }

        // Sélection aléatoire
        $randomIndex = array_rand($games);
        return $games[$randomIndex];
    }
}
