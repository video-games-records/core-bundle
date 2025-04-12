<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Ranking\Provider\Player;

use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\PlayerSerie;
use VideoGamesRecords\CoreBundle\Entity\Serie;
use VideoGamesRecords\CoreBundle\Ranking\Provider\AbstractRankingProvider;

class PlayerSerieRankingProvider extends AbstractRankingProvider
{
    /**
     * @param int|null $id
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingPoints(?int $id = null, array $options = []): array
    {
        $serie = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Serie')->find($id);
        if (null == $serie) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $player = $this->getPlayer($options['user'] ?? null);
        $limit = $options['limit'] ?? null;

        $query = $this->em->createQueryBuilder()
            ->select('ps')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerSerie', 'ps')
            ->join('ps.player', 'p')
            ->addSelect('p')
            ->orderBy('ps.rankPointChart');

        $query->where('ps.serie = :serie')
            ->setParameter('serie', $serie);

        /** @var PlayerSerie $row */
        $row = (null !== $player) ? $this->getRow($serie, $player) : null;

        if (null !== $maxRank) {
            if (null !== $row) {
                $query->andWhere('(ps.rankPointChart <= :maxRank OR ps.rankPointChart BETWEEN :min AND :max)')
                    ->setParameter('min', $row->getRankPointChart() - 5)
                    ->setParameter('max', $row->getRankPointChart() + 5);
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
        $player = $this->getPlayer($options['user'] ?? null);
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
