<?php

namespace VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player;

use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\AbstractRankingQuery;

class PlayerRankingQuery extends AbstractRankingQuery
{
    /**
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingPointChart(array $options = []): array
    {
        return $this->getRanking('rankPointChart', $options);
    }

    /**
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingPointGame(array $options = []): array
    {
        return $this->getRanking('rankPointGame', $options);
    }

    /**
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingMedals(array $options = []): array
    {
        return $this->getRanking('rankMedal', $options);
    }

    /**
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingBadge(array $options = []): array
    {
        return $this->getRanking('rankBadge', $options);
    }

    /**
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingCup(array $options = []): array
    {
        return $this->getRanking('rankCup', $options);
    }

    /**
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingProof(array $options = []): array
    {
        return $this->getRanking('rankProof', $options);
    }

    /**
     * @param string $column
     * @param array  $options
     * @return array
     * @throws ORMException
     */
    private function getRanking(string $column = 'rankPointChart', array $options = []): array
    {
        $maxRank = $options['maxRank'] ?? 100;
        $limit = $options['limit'] ?? null;
        $player = $this->getPlayer();
        $team = !empty($options['idTeam']) ? $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $options['idTeam']) : null;

        $query = $this->em->createQueryBuilder()
            ->select('p')
            ->from('VideoGamesRecords\CoreBundle\Entity\Player', 'p')
            ->leftJoin('p.team', 't')
            ->addSelect('t')
            ->leftJoin('p.country', 'c')
            ->addSelect('c')
            ->leftJoin('c.translations', 'trans')
            ->addSelect('trans')
            ->where("p.$column != 0")
            ->orderBy("p.$column");

        if ($team !== null) {
            $query->andWhere('(p.team = :team)')
                ->setParameter('team', $team);
        } elseif (($maxRank !== null) && ($player !== null)) {
            $query->andWhere("(p.$column <= :maxRank OR p = :player)")
                ->setParameter('maxRank', $maxRank)
                ->setParameter('player', $player);
        } else {
            $query->andWhere("p.$column <= :maxRank")
                ->setParameter('maxRank', $maxRank);
        }

        if (null !== $limit) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }

    public function getRankingPoints(int $id = null, array $options = []): array
    {
        // TODO: Implement getRankingPoints() method.
    }
}
