<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use VideoGamesRecords\CoreBundle\Entity\Game;

class GameRepository extends EntityRepository
{
    /**
     * Finds games begining with a letter.
     *
     * @param string $letter
     * @return array
     */
    public function findWithLetter($letter)
    {
        $query = $this->createQueryBuilder('g')
            ->addSelect('translation');
        if ($letter == '0') {
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
        $query->orderBy('translation.name', 'ASC');

        $this->onlyActive($query);
        $this->withPlatforms($query);

        return $query->getQuery()->getResult();
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
}
