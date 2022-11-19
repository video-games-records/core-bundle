<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Read;

use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Serie;

class PlayerSerieRankingQuery extends DefaultRankingQuery
{
    public function getRankingPoints(int $id = null, array $options = []): array
    {
        $serie = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Serie')->find($id);
        if (null === $serie) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $player = $this->getPlayer();
        $limit = $options['limit'] ?? null;

        $query = $this->em->createQueryBuilder()
            ->select('ps')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerSerie', 'ps')
            ->join('ps.player', 'p')
            ->addSelect('p')
            ->orderBy('ps.rankPointChart');

        $query->where('ps.serie = :serie')
            ->setParameter('serie', $serie);

        $row = (null !== $player) ? $this->getRow($serie, $player) : null;

        if (null !== $maxRank) {
            if (null !== $row) {
                $query->andWhere('(ps.rankPointChart <= :maxRank OR ps.rankPointChart BETWEEN :min AND :max)')
                    ->setParameter('min', $row->getRankPoint() - 5)
                    ->setParameter('max', $row->getRankPoint() + 5);
            } else {
                $query->andWhere('ps.rankPointChart <= :maxRank');
            }
            $query->setParameter('maxRank', $maxRank);
        }

        if (null !== $limit) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }

    public function getRankingMedals(int $id = null, array $options = []): array
    {
        $serie = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Serie')->find($id);
        if (null === $serie) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $player = $this->getPlayer();
        $limit = $options['limit'] ?? null;

        $query = $this->em->createQueryBuilder()
            ->select('ps')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerSerie', 'ps')
            ->join('ps.player', 'p')
            ->addSelect('p')
            ->orderBy('ps.rankMedal');

        $query->where('ps.serie = :serie')
            ->setParameter('serie', $serie);

        $row = (null !== $player) ? $this->getRow($serie, $player) : null;

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

    private function getRow(Serie $serie, Player $player)
    {
        return $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerSerie')->findOneBy(
            [
                'serie' => $serie,
                'player' => $player,
            ]
        );
    }
}
