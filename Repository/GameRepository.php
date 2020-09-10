<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use VideoGamesRecords\CoreBundle\Entity\Game;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\DBAL\DBALException;

class GameRepository extends EntityRepository
{

    /**
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getStats()
    {
        $qb = $this->createQueryBuilder('game')
            ->select('COUNT(game.id)');
        $qb->where('game.status = :status')
            ->setParameter('status', Game::STATUS_ACTIVE);

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
    public function findWithLetter(string $letter,string $locale = 'en')
    {
        $query = $this->createQueryBuilder('g')
            ->addSelect('translation');
        if ($letter === '0') {
            $query
                ->innerJoin('g.translations', 'translation')
                ->where('SUBSTRING(translation.name , 1, 1) NOT IN (:list)')
                ->setParameter('list', range('a', 'z'));
        } else {
            $query
                ->innerJoin('g.translations', 'translation')
                ->where('SUBSTRING(translation.name , 1, 1) = :letter')
                ->setParameter('letter', $letter);
        }
        $query
            ->andWhere('translation.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('translation.name', 'ASC');

        $this->onlyActive($query);
        $this->withPlatforms($query);

        return $query->getQuery();
    }

    /**
     * Finds games in the given series.
     *
     * @param int $idSerie
     * @return array
     */
    public function findSerieGames(int $idSerie)
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
     * @param        $player
     * @param string $locale
     * @return int|mixed|string
     */
    public function findFromlostPosition($player, $locale = 'en')
    {
        $query = $this->createQueryBuilder('game');
        $query
            ->addSelect('translation')
            ->innerJoin('game.translations', 'translation')
            ->innerJoin('game.groups', 'group')
            ->innerJoin('group.charts', 'chart')
            ->innerJoin('chart.lostPositions', 'lostPosition')
            ->where('lostPosition.player = :player')
            ->setParameter('player', $player)
            ->andWhere('translation.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('translation.name', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Requires only active games.
     * @param QueryBuilder $query
     */
    private function onlyActive(QueryBuilder $query)
    {
        $query
            ->andWhere('g.status = :status')
            ->setParameter('status', Game::STATUS_ACTIVE);
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
     * @param $date1
     * @param $date2
     * @return array
     */
    public function getNbPostDay($date1, $date2)
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
     * @throws DBALException
     */
    public function copy($id)
    {
        $sql = sprintf("call copy_game (%d);", $id);
        $this->_em->getConnection()->executeUpdate($sql);
    }
}
