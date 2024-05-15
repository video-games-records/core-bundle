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
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\ValueObject\ChartStatus;
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
        $query = $this->_em->createQuery("
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

    /**
     * @param Game $game
     */
    public function copy(Game $game): void
    {
        $newGame = new Game();
        $newGame->setLibGameEn($game->getLibGameEn() . ' [COPY]');
        $newGame->setLibGameFr($game->getLibGameFr() . ' [COPY]');
        $newGame->setSerie($game->getSerie());

        foreach ($game->getPlatforms() as $platform) {
            $newGame->addPlatform($platform);
        }

        /** @var Group $group */
        foreach ($game->getGroups() as $group) {
            $newGroup = new Group();
            $newGroup->setLibGroupEn($group->getLibGroupEn());
            $newGroup->setLibGroupFr($group->getLibGroupFr());
            $newGroup->setIsDlc($group->getIsDlc());

            /** @var Chart $chart */
            foreach ($group->getCharts() as $chart) {
                $newChart = new Chart();
                $newChart->setLibChartEn($chart->getLibChartEn());
                $newChart->setLibChartFr($chart->getLibChartFr());

                /** @var ChartLib $lib */
                foreach ($chart->getLibs() as $lib) {
                    $newLib = new ChartLib();
                    $newLib->setName($lib->getName());
                    $newLib->setType($lib->getType());
                    $newChart->addLib($newLib);
                }
                $newGroup->addChart($newChart);
            }

            $newGame->addGroup($newGroup);
        }
        $this->_em->persist($newGame);
        $this->_em->flush();
    }

    /**
     * @param game $game
     */
    public function maj(Game $game): void
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->update('VideoGamesRecords\CoreBundle\Entity\Chart', 'c')
            ->set('c.statusPlayer', ':status')
            ->set('c.statusTeam', ':status')
            ->setParameter('status', ChartStatus::MAJ)
            ->where('c.group IN (
                            SELECT g FROM VideoGamesRecords\CoreBundle\Entity\Group g
                        WHERE g.game = :game)')
            ->setParameter('game', $game);

        $query->getQuery()->execute();
    }


    /**
     * @param        $game
     */
    public function setChartToMajPlayer($game): void
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->update('VideoGamesRecords\CoreBundle\Entity\Chart', 'c')
            ->set('c.statusPlayer', ':status')
            ->setParameter('status', ChartStatus::MAJ)
            ->where('c.group IN (
                            SELECT g FROM VideoGamesRecords\CoreBundle\Entity\Group g
                        WHERE g.game = :game)')
            ->setParameter('game', $game);

        $query->getQuery()->execute();
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
}
