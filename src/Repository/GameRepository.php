<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Proof;
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
    public function getIds() : array
    {
        return $this->createQueryBuilder('game')
            ->select('game.id')
            ->where('game.status = :status')
            ->setParameter('status', GameStatus::STATUS_ACTIVE)
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
        $this->whereStatus($qb, GameStatus::STATUS_CREATED);
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
        $this->whereStatus($qb, GameStatus::STATUS_ADD_PICTURE);
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
        $this->whereStatus($qb, GameStatus::STATUS_ADD_SCORE);
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
        $this->whereStatus($qb, GameStatus::STATUS_COMPLETED);
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
        $this->whereStatus($qb, GameStatus::STATUS_ACTIVE);
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
        $this->whereStatus($qb, GameStatus::STATUS_INACTIVE);
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
            ->setParameter('status', GameStatus::STATUS_ACTIVE);

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
     * @param $id
     * @throws Exception
     */
    public function copy($id)
    {
        $sql = sprintf("call copy_game (%d);", $id);
        $this->_em->getConnection()->executeStatement($sql);
    }

    /**
     * @return int|mixed|string
     */
    public function getNbProofInProgress()
    {
        $qb = $this->createQueryBuilder('gam')
            ->select('gam')
            ->addSelect('COUNT(proof) as nb')
            ->innerJoin('gam.groups', 'grp')
            ->innerJoin('grp.charts', 'chr')
            ->innerJoin('chr.playerCharts', 'pc')
            ->innerJoin('pc.proof', 'proof')
            ->where('proof.status = :status')
            ->setParameter('status', Proof::STATUS_IN_PROGRESS)
            ->groupBy('gam.id')
            ->orderBy('nb', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param game $game
     */
    public function maj(Game $game)
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->update('VideoGamesRecords\CoreBundle\Entity\Chart', 'c')
            ->set('c.statusPlayer', ':status')
            ->set('c.statusTeam', ':status')
            ->setParameter('status', ChartStatus::STATUS_MAJ)
            ->where('c.group IN (
                            SELECT g FROM VideoGamesRecords\CoreBundle\Entity\Group g
                        WHERE g.game = :game)')
            ->setParameter('game', $game);

        $query->getQuery()->execute();
    }


    /**
     * @param        $game
     */
    public function setChartToMajPlayer($game)
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->update('VideoGamesRecords\CoreBundle\Entity\Chart', 'c')
            ->set('c.statusPlayer', ':status')
            ->setParameter('status', ChartStatus::STATUS_MAJ)
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
    private function onlyActive(QueryBuilder $query)
    {
        $query
            ->andWhere('g.status = :status')
            ->setParameter('status', GameStatus::STATUS_ACTIVE);
    }

    /**
     * @param QueryBuilder $query
     * @param string       $status
     */
    private function whereStatus(QueryBuilder $query, string $status)
    {
        $query
            ->andWhere('g.status = :status')
            ->setParameter('status', $status);
    }

    /**
     * Adds platforms in the output to fasten display.
     * @param QueryBuilder $query
     */
    private function withPlatforms(QueryBuilder $query)
    {
        $query->join('g.platforms', 'p')
            ->addSelect('p');
    }

    /**
     * @param QueryBuilder $query
     * @param string       $locale
     */
    private function setOrder(QueryBuilder $query, string $locale = 'en')
    {
        $column = ($locale == 'fr') ? 'libGameFr' : 'libGameEn';
        $query->orderBy("g.$column", 'ASC');
    }
}
