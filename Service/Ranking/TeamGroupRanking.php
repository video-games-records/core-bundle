<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Interface\RankingInterface;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class TeamGroupRanking implements RankingInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function maj($id): void
    {
        $group = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Group')->find($id);
        if (null === $group) {
            return;
        }

        //----- delete
        $query = $this->em->createQuery(
            'DELETE VideoGamesRecords\CoreBundle\Entity\TeamGroup tg WHERE tg.group = :group'
        );
        $query->setParameter('group', $group);
        $query->execute();

        //----- select ans save result in array
        $query = $this->em->createQuery("
            SELECT
                t.id,
                '' as rankPointChart,
                '' as rankMedal,
                SUM(tc.chartRank0) as chartRank0,
                SUM(tc.chartRank1) as chartRank1,
                SUM(tc.chartRank2) as chartRank2,
                SUM(tc.chartRank3) as chartRank3,
                SUM(tc.pointChart) as pointChart
            FROM VideoGamesRecords\CoreBundle\Entity\TeamChart tc
            JOIN tc.chart c
            JOIN tc.team t
            WHERE c.group = :group
            GROUP BY t.id
            ORDER BY pointChart DESC");


        $query->setParameter('group', $group);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $list[] = $row;
        }

        //----- add some data
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart'], true);
        $list = Ranking::order($list, ['chartRank0' => SORT_DESC, 'chartRank1' => SORT_DESC, 'chartRank2' => SORT_DESC, 'chartRank3' => SORT_DESC]);
        $list = Ranking::addRank($list, 'rankMedal', ['chartRank0', 'chartRank1', 'chartRank2', 'chartRank3']);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        foreach ($list as $row) {
            $teamGroup = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\TeamGroup'
            );
            $teamGroup->setTeam($this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $row['id']));
            $teamGroup->setGroup($group);

            $this->em->persist($teamGroup);
        }
        $this->em->flush();
    }

    public function get($id): void
    {
        // TODO: Implement get() method.
    }

    public function getRankingPoints(int $id, array $options = []): array
    {
        // TODO: Implement getRankingPoints() method.
    }

    public function getRankingMedals(int $id, array $options = []): array
    {
        // TODO: Implement getRankingMedals() method.
    }
}
