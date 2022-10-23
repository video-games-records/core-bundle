<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Interface\RankingSelectInterface;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class PlayerPlatformRankingSelect implements RankingSelectInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getRankingPoints(int $id = null, array $options = []): array
    {
        $platform = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Platform')->find($id);
        if (null === $platform) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $player = $options['player'] ?? null;

        $query = $this->em->createQueryBuilder()
            ->select('pp')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerPlatform', 'pp')
            ->join('pp.player', 'p')
            ->addSelect('p')
            ->orderBy('pp.rankPointPlatform');

        $query->where('pp.platform = :platform')
            ->setParameter('platform', $platform);

        if (($maxRank !== null) && ($player !== null)) {
            $query->andWhere('(pp.rankPointPlatform <= :maxRank OR pp.player = :player)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('player', $player);
        } elseif ($maxRank !== null) {
            $query->andWhere('pp.rankPointPlatform <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        }
        return $query->getQuery()->getResult();
    }

    public function getRankingMedals(int $id = null, array $options = []): array
    {
        return [];
    }
}
