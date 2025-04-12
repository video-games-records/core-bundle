<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Ranking\Provider\Team;

use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Entity\TeamSerie;
use VideoGamesRecords\CoreBundle\Entity\Serie;
use VideoGamesRecords\CoreBundle\Ranking\Provider\AbstractRankingProvider;

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
        $serie = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Serie')->find($id);
        if (null === $serie) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $team = $this->getTeam($options['user'] ?? null);
        $limit = $options['limit'] ?? null;

        $query = $this->em->createQueryBuilder()
            ->select('ps')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerSerie', 'ps')
            ->join('ps.player', 'p')
            ->addSelect('p')
            ->orderBy('ps.rankMedal');

        $query->where('ps.serie = :serie')
            ->setParameter('serie', $serie);

        $row = (null !== $team) ? $this->getRow($serie, $team) : null;

        if (null !== $maxRank) {
            if (null !== $row) {
                $query->andWhere('(ps.rankMedal <= :maxRank OR ps.rankMedal BETWEEN :min AND :max)')
                    ->setParameter('min', $row->getRankPoint() - 5)
                    ->setParameter('max', $row->getRankPoint() + 5);
            } else {
                $query->andWhere('ps.rankMedal <= :maxRank');
            }
            $query->setParameter('maxRank', $maxRank);
        }

        if (null !== $limit) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }

    private function getRow(Serie $serie, Team $team)
    {
        return $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\TeamSerie')->findOneBy(
            [
                'serie' => $serie,
                'team' => $team,
            ]
        );
    }
}
