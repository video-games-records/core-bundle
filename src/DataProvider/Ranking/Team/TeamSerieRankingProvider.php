<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataProvider\Ranking\Team;

use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\AbstractRankingProvider;
use VideoGamesRecords\CoreBundle\Entity\Serie;

class TeamSerieRankingProvider extends AbstractRankingProvider
{
    /**
     * @param int|null $id
     * @param array $options
     * @return array
     */
    public function getRankingPoints(?int $id = null, array $options = []): array
    {
        $serie = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Serie')->find($id);
        if (null == $serie) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $limit = $options['limit'] ?? null;

        $query = $this->em->createQueryBuilder()
            ->select('ts')
            ->from('VideoGamesRecords\CoreBundle\Entity\TeamSerie', 'ts')
            ->join('ts.team', 't')
            ->addSelect('t')
            ->orderBy('ts.rankPointChart');

        $query->where('ts.serie = :serie')
            ->setParameter('serie', $serie);

        if (null !== $maxRank) {
            $query->andWhere('ts.rankPointChart <= :maxRank');
            $query->setParameter('maxRank', $maxRank);
        }

        if (null !== $limit) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param int|null $id
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingMedals(?int $id = null, array $options = []): array
    {
        /** @var Serie $serie */
        $serie = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Serie')->find($id);
        if (null === $serie) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $limit = $options['limit'] ?? null;

        $query = $this->em->createQueryBuilder()
            ->select('ts')
            ->from('VideoGamesRecords\CoreBundle\Entity\TeamSerie', 'ts')
            ->join('ts.team', 't')
            ->addSelect('t')
            ->orderBy('ts.rankMedal');

        $query->where('ts.serie = :serie')
            ->setParameter('serie', $serie);

        if (null !== $maxRank) {
            $query->andWhere('ts.rankPointChart <= :maxRank');
            $query->setParameter('maxRank', $maxRank);
        }

        if (null !== $limit) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }
}
