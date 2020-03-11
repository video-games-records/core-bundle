<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use VideoGamesRecords\CoreBundle\Entity\Game;

class GameRepository extends EntityRepository
{

    /**
     * @return mixed
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
     * @return \Doctrine\ORM\Query
     */
    public function findWithLetter($letter, $locale)
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
    public function findSerieGames($idSerie)
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
     * Requires only active games.
     *
     * @param \Doctrine\ORM\QueryBuilder $query
     */
    private function onlyActive(QueryBuilder $query)
    {
        $query
            ->andWhere('g.status = :status')
            ->setParameter('status', Game::STATUS_ACTIVE);
    }

    /**
     * Adds platforms in the output to fasten display.
     *
     * @param \Doctrine\ORM\QueryBuilder $query
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
                 COUNT(pc.idChart) as nb
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
}
