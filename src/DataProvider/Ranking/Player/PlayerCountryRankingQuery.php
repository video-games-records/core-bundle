<?php

namespace VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player;

use VideoGamesRecords\CoreBundle\DataProvider\Ranking\AbstractRankingQuery;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingQueryInterface;

class PlayerCountryRankingQuery extends AbstractRankingQuery implements RankingQueryInterface
{
    /**
     * @param int|null $id
     * @param array    $options
     * @return array
     */
    public function getRankingPoints(int $id = null, array $options = []): array
    {
        $country = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Country')->find($id);
        if (null === $country) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;

        $query = $this->em->createQueryBuilder('p')
            ->select('p')
            ->from('VideoGamesRecords\CoreBundle\Entity\Player', 'p')
            ->where('(p.country = :country)')
            ->setParameter('country', $country)
            ->orderBy('p.rankCountry');

        if ($maxRank !== null) {
            $query->andWhere('p.rankCountry <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults($maxRank);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param int|null $id
     * @param array $options
     * @return array
     */
    public function getRankingMedals(int $id = null, array $options = []): array
    {
        return array();
    }
}
