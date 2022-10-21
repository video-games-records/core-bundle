<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Interface\RankingInterface;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class PlayerGroupRanking implements RankingInterface
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
        $query = $this->em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\PlayerGroup pg WHERE pg.group = :group');
        $query->setParameter('group', $group);
        $query->execute();

        $data = [];

        //----- data rank0
        $query = $this->em->createQuery("
            SELECT
                 p.id,
                 COUNT(pc.id) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            JOIN pc.chart c
            WHERE c.group = :group
            AND pc.rank = 1
            AND c.nbPost > 1
            AND pc.nbEqual = 1
            GROUP BY p.id");


        $query->setParameter('group', $group);
        $result = $query->getResult();
        foreach ($result as $row) {
            $data['chartRank0'][$row['id']] = $row['nb'];
        }

        //----- data rank1 to rank5
        $query = $this->em->createQuery("
            SELECT
                 p.id,
                 COUNT(pc.id) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            JOIN pc.chart c
            WHERE c.group = :group
            AND pc.rank = :rank
            GROUP BY p.id");
        $query->setParameter('group', $group);

        for ($i = 1; $i <= 5; $i++) {
            $query->setParameter('rank', $i);
            $result = $query->getResult();
            foreach ($result as $row) {
                $data["chartRank$i"][$row['id']] = $row['nb'];
            }
        }

        //----- data nbRecordProuve
        $query = $this->em->createQuery("
            SELECT
                 p.id,
                 COUNT(pc.id) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            JOIN pc.chart c
            WHERE c.group = :group
            AND pc.status = :status
            GROUP BY p.id");

        $query->setParameter('group', $group);
        $query->setParameter(
            'status',
            $this->em->getReference(
                PlayerChartStatus::class,
                PlayerChartStatus::ID_STATUS_PROOVED
            )
        );

        $result = $query->getResult();
        foreach ($result as $row) {
            $data['nbChartProven'][$row['id']] = $row['nb'];
        }


        //----- select and save result in array
        $query = $this->em->createQuery("
            SELECT
                p.id,
                '' as rankPoint,
                '' as rankMedal,
                SUM(pc.pointChart) as pointChart,
                COUNT(pc.id) as nbChart,
                MAX(pc.lastUpdate) as lastUpdate
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            JOIN pc.chart c
            WHERE c.group = :group
            GROUP BY p.id
            ORDER BY pointChart DESC");


        $query->setParameter('group', $group);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $row['rankMedal'] = 0;
            $row['chartRank0'] = (isset($data['chartRank0'][$row['id']])) ? $data['chartRank0'][$row['id']] : 0;
            $row['chartRank1'] = (isset($data['chartRank1'][$row['id']])) ? $data['chartRank1'][$row['id']] : 0;
            $row['chartRank2'] = (isset($data['chartRank2'][$row['id']])) ? $data['chartRank2'][$row['id']] : 0;
            $row['chartRank3'] = (isset($data['chartRank3'][$row['id']])) ? $data['chartRank3'][$row['id']] : 0;
            $row['chartRank4'] = (isset($data['chartRank4'][$row['id']])) ? $data['chartRank4'][$row['id']] : 0;
            $row['chartRank5'] = (isset($data['chartRank5'][$row['id']])) ? $data['chartRank5'][$row['id']] : 0;
            $row['nbChartProven'] = (isset($data['nbChartProven'][$row['id']])) ? $data['nbChartProven'][$row['id']] : 0;
            $row['lastUpdate'] = new DateTime($row['lastUpdate']);
            $list[] = $row;
        }

        //----- add some data
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart']);
        $list = Ranking::order($list, ['chartRank0' => SORT_DESC, 'chartRank1' => SORT_DESC, 'chartRank2' => SORT_DESC, 'chartRank3' => SORT_DESC]);
        $list = Ranking::addRank($list, 'rankMedal', ['chartRank0', 'chartRank1', 'chartRank2', 'chartRank3', 'chartRank4', 'chartRank5']);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        foreach ($list as $row) {
            $playerGroup = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\PlayerGroup'
            );
            $playerGroup->setPlayer($this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $row['id']));
            $playerGroup->setGroup($group);

            $this->em->persist($playerGroup);
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
