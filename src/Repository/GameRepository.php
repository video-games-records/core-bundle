<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\IgdbBundle\Entity\Game as IgdbGame;
use VideoGamesRecords\CoreBundle\ValueObject\GameStatus;

class GameRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * @return array
     */
    public function getIds(): array
    {
        return $this->createQueryBuilder('game')
            ->select('game.id')
            ->where('game.status = :status')
            ->setParameter('status', GameStatus::ACTIVE)
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @return mixed
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countStatusCreated(): mixed
    {
        $qb = $this->getCountQueryBuilder();
        $this->whereStatus($qb, GameStatus::CREATED);
        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return mixed
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countStatusAddPicture(): mixed
    {
        $qb = $this->getCountQueryBuilder();
        $this->whereStatus($qb, GameStatus::ADD_PICTURE);
        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return mixed
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countStatusAddScore(): mixed
    {
        $qb = $this->getCountQueryBuilder();
        $this->whereStatus($qb, GameStatus::ADD_SCORE);
        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return mixed
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countStatusCompleted(): mixed
    {
        $qb = $this->getCountQueryBuilder();
        $this->whereStatus($qb, GameStatus::COMPLETED);
        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countStatusActive(): mixed
    {
        $qb = $this->getCountQueryBuilder();
        $this->whereStatus($qb, GameStatus::ACTIVE);
        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countStatusInactive(): mixed
    {
        $qb = $this->getCountQueryBuilder();
        $this->whereStatus($qb, GameStatus::INACTIVE);
        return $qb->getQuery()
            ->getSingleScalarResult();
    }


    /**
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getStats(): mixed
    {
        $qb = $this->createQueryBuilder('game')
            ->select('COUNT(game.id)');
        $qb->where('game.status = :status')
            ->setParameter('status', GameStatus::ACTIVE);

        return $qb->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Finds games begining with a letter.
     *
     * @param string $letter
     * @param string $locale
     *
     * @return Query
     */
    public function findWithLetter(string $letter, string $locale = 'en'): Query
    {
        $column = ($locale == 'fr') ? 'libGameFr' : 'libGameEn';
        $query = $this->createQueryBuilder('g');
        if ($letter === '0') {
            $query->where("SUBSTRING(g.$column , 1, 1) NOT IN (:list)")
                ->setParameter('list', range('a', 'z'));
        } else {
            $query->where("SUBSTRING(g.$column , 1, 1) = :letter")
                ->setParameter('letter', $letter);
        }

        $this->setOrder($query, $locale);
        $this->onlyActive($query);
        $this->withPlatforms($query);

        return $query->getQuery();
    }

    /**
     * @param string $q
     * @param string $locale
     * @return mixed
     */
    public function autocomplete(string $q, string $locale = 'en'): mixed
    {
        $column = ($locale == 'fr') ? 'libGameFr' : 'libGameEn';
        $query = $this->createQueryBuilder('g');

        $query
            ->where("g.$column LIKE :q")
            ->setParameter('q', '%' . $q . '%')
            ->orderBy("g.$column", 'ASC');

        return $query->getQuery()->getResult();
    }

    /**
     * Finds games in the given series.
     *
     * @param int $idSerie
     * @return array
     */
    public function findSerieGames(int $idSerie): array
    {
        $query = $this->createQueryBuilder('g');
        $query
            ->where('g.idSerie = :idSerie')
            ->setParameter('idSerie', $idSerie);

        $this->onlyActive($query);
        $this->withPlatforms($query);

        return $query->getQuery()->getResult();
    }

    /**
     * @param $date1
     * @param $date2
     * @return array
     */
    public function getNbPostDay($date1, $date2): array
    {
        //----- data nbPostDay
        $query = $this->getEntityManager()->createQuery("
            SELECT
                 ga.id,
                 COUNT(pc.id) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.chart c
            JOIN c.group gr
            JOIN gr.game ga
            WHERE pc.lastUpdate BETWEEN :date1 AND :date2
            GROUP BY ga.id");


        $query->setParameter('date1', $date1);
        $query->setParameter('date2', $date2);
        $result = $query->getResult();

        $data = array();
        foreach ($result as $row) {
            $data[$row['id']] = $row['nb'];
        }

        return $data;
    }


    /*************************************/
    /************  PRIVATE  **************/
    /*************************************/

    /**
     * @return QueryBuilder
     */
    private function getCountQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('g')
            ->select('COUNT(g.id)');
    }

    /**
     * Requires only active games.
     * @param QueryBuilder $query
     */
    private function onlyActive(QueryBuilder $query): void
    {
        $query
            ->andWhere('g.status = :status')
            ->setParameter('status', GameStatus::ACTIVE);
    }

    /**
     * @param QueryBuilder $query
     * @param string       $status
     */
    private function whereStatus(QueryBuilder $query, string $status): void
    {
        $query
            ->andWhere('g.status = :status')
            ->setParameter('status', $status);
    }

    /**
     * Adds platforms in the output to fasten display.
     * @param QueryBuilder $query
     */
    private function withPlatforms(QueryBuilder $query): void
    {
        $query->join('g.platforms', 'p')
            ->addSelect('p');
    }

    /**
     * @param QueryBuilder $query
     * @param string       $locale
     */
    private function setOrder(QueryBuilder $query, string $locale = 'en'): void
    {
        $column = ($locale == 'fr') ? 'libGameFr' : 'libGameEn';
        $query->orderBy("g.$column", 'ASC');
    }

    // IGDB Mapping Methods

    /**
     * Find IGDB Game entity for a VGR Game ID
     */
    public function findIgdbGame(int $vgrGameId): ?IgdbGame
    {
        try {
            return $this->createQueryBuilder('g')
                ->select('g, ig')
                ->leftJoin('g.igdbGame', 'ig')
                ->where('g.id = :id')
                ->setParameter('id', $vgrGameId)
                ->getQuery()
                ->getSingleResult()
                ?->getIgdbGame();
        } catch (NoResultException|NonUniqueResultException) {
            return null;
        }
    }

    /**
     * Find IGDB ID for a VGR Game ID
     */
    public function findIgdbId(int $vgrGameId): ?int
    {
        return $this->findIgdbGame($vgrGameId)?->getId();
    }

    /**
     * Find VGR Game by IGDB Game entity
     */
    public function findVgrGameByIgdbGame(IgdbGame $igdbGame): ?Game
    {
        try {
            return $this->createQueryBuilder('g')
                ->where('g.igdbGame = :igdbGame')
                ->setParameter('igdbGame', $igdbGame)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException|NonUniqueResultException) {
            return null;
        }
    }

    /**
     * Find VGR Game ID by IGDB ID
     */
    public function findVgrIdByIgdbId(int $igdbGameId): ?int
    {
        try {
            $result = $this->createQueryBuilder('g')
                ->select('g.id')
                ->leftJoin('g.igdbGame', 'ig')
                ->where('ig.id = :igdbId')
                ->setParameter('igdbId', $igdbGameId)
                ->getQuery()
                ->getSingleScalarResult();
            
            return $result ? (int) $result : null;
        } catch (NoResultException|NonUniqueResultException) {
            return null;
        }
    }

    /**
     * Find all VGR Game IDs that have IGDB mappings
     */
    public function findVgrIdsWithIgdbMapping(): array
    {
        $results = $this->createQueryBuilder('g')
            ->select('g.id')
            ->where('g.igdbGame IS NOT NULL')
            ->getQuery()
            ->getResult();

        return array_column($results, 'id');
    }

    /**
     * Find all IGDB Game IDs that are mapped from VGR
     */
    public function findMappedIgdbIds(): array
    {
        $results = $this->createQueryBuilder('g')
            ->select('ig.id')
            ->leftJoin('g.igdbGame', 'ig')
            ->where('g.igdbGame IS NOT NULL')
            ->getQuery()
            ->getResult();

        return array_column($results, 'id');
    }

    /**
     * Update IGDB Game for a VGR Game
     */
    public function updateIgdbGame(int $vgrGameId, ?IgdbGame $igdbGame): bool
    {
        try {
            $game = $this->find($vgrGameId);
            if (!$game) {
                return false;
            }

            $game->setIgdbGame($igdbGame);
            $this->getEntityManager()->flush();

            return true;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Update IGDB ID for a VGR Game (compatibility method)
     */
    public function updateIgdbId(int $vgrGameId, ?int $igdbGameId): bool
    {
        try {
            $igdbGame = null;
            if ($igdbGameId !== null) {
                $igdbGame = $this->getEntityManager()
                    ->getRepository(IgdbGame::class)
                    ->find($igdbGameId);
                
                if (!$igdbGame) {
                    return false; // IGDB Game not found
                }
            }

            return $this->updateIgdbGame($vgrGameId, $igdbGame);
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Get IGDB mapping statistics
     */
    public function getIgdbMappingStats(): array
    {
        try {
            $total = $this->createQueryBuilder('g')
                ->select('COUNT(g.id)')
                ->getQuery()
                ->getSingleScalarResult();

            $mapped = $this->createQueryBuilder('g')
                ->select('COUNT(g.id)')
                ->where('g.igdbGame IS NOT NULL')
                ->getQuery()
                ->getSingleScalarResult();

            $unmapped = $total - $mapped;
            $coverage = $total > 0 ? round(($mapped / $total) * 100, 2) : 0;

            return [
                'total' => (int) $total,
                'mapped' => (int) $mapped,
                'unmapped' => (int) $unmapped,
                'coverage' => $coverage
            ];
        } catch (Exception) {
            return [
                'total' => 0,
                'mapped' => 0,
                'unmapped' => 0,
                'coverage' => 0
            ];
        }
    }

    /**
     * Batch update IGDB mappings
     */
    public function batchUpdateIgdbMappings(array $mappings): int
    {
        $updated = 0;
        
        foreach ($mappings as $vgrGameId => $igdbGameId) {
            if ($this->updateIgdbId($vgrGameId, $igdbGameId)) {
                $updated++;
            }
        }

        return $updated;
    }
}
