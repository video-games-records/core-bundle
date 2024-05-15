<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ObjectManager;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;
use VideoGamesRecords\CoreBundle\Entity\ChartType;

class ChartFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            GameFixtures::class
        ];
    }

    /**
     * @var array<string>
     */
    private array $entities = [
        'Group',
    ];


    /**
     * @var array<mixed>
     */
    private array $types = [
        [
            'id'  => 1,
            'name'    => 'Score',
            'mask'    => '30~',
            'order_by' => 'DESC',
        ],
        [
            'id'  => 2,
            'name'    => 'Temps',
            'mask'    => '30~:|2~.|2~',
            'order_by' => 'ASC',
        ],
        [
            'id'  => 3,
            'name'    => 'Distance',
            'mask'    => '30~ m',
            'order_by' => 'DESC',
        ],
    ];


    /**
     * @var array<mixed>
     */
    private array $charts = [
        [
            'id'   => 1,
            'group_id'   => 1,
            'LibChartEn' => 'Baby Park',
            'libChartFr' => 'Baby Park',
            'types'     => [1],
        ],
        [
            'id'   => 2,
            'group_id'   => 1,
            'LibChartEn' => 'Bowser\'s Castle',
            'libChartFr' => 'Bowser\'s Castle',
            'types'     => [1],
        ],
        [
            'id'   => 3,
            'group_id'   => 1,
            'LibChartEn' => 'Daisy Cruiser',
            'libChartFr' => 'Daisy Cruiser',
            'types'     => [2],
        ],
        [
            'id'   => 4,
            'group_id'   => 1,
            'LibChartEn' => 'Dino Dino Jungle',
            'libChartFr' => 'Dino Dino Jungle',
            'types'     => [3],
        ],
        [
            'id'   => 5,
            'group_id'   => 1,
            'LibChartEn' => 'DK Mountain',
            'libChartFr' => 'DK Mountain',
            'types'     => [1],
        ],
        [
            'id'   => 6,
            'group_id'   => 1,
            'LibChartEn' => 'Dry Dry Desert',
            'libChartFr' => 'Dry Dry Desert',
            'types'     => [2],
        ],
        [
            'id'   => 7,
            'group_id'   => 1,
            'LibChartEn' => 'Luigi Circuit',
            'libChartFr' => 'Luigi Circuit',
            'types'     => [1, 2],
        ],
        [
            'id'   => 8,
            'group_id'   => 1,
            'LibChartEn' => 'Mario Circuit',
            'libChartFr' => 'Mario Circuit',
            'types'     => [1, 3],
        ],
        [
            'id'   => 9,
            'group_id'   => 1,
            'LibChartEn' => 'Mushroom Bridge',
            'libChartFr' => 'Mushroom Bridge',
            'types'     => [2, 3],
        ],
        [
            'id'   => 10,
            'group_id'   => 1,
            'LibChartEn' => 'Mushroom City',
            'libChartFr' => 'Mushroom City',
            'types'     => [1],
        ],
        [
            'id'   => 11,
            'group_id'   => 1,
            'LibChartEn' => 'Peach Beach',
            'libChartFr' => 'Peach Beach',
            'types'     => [1],
        ],
        [
            'id'   => 12,
            'group_id'   => 1,
            'LibChartEn' => 'Rainbow Road',
            'libChartFr' => 'Rainbow Road',
            'types'     => [1],
        ],
        [
            'id'   => 13,
            'group_id'   => 1,
            'LibChartEn' => 'Sherbet Land',
            'libChartFr' => 'Sherbet Land',
            'types'     => [1],
        ],
        [
            'id'   => 14,
            'group_id'   => 1,
            'LibChartEn' => 'Waluigi Stadium',
            'libChartFr' => 'Waluigi Stadium',
            'types'     => [1],
        ],
        [
            'id'   => 15,
            'group_id'   => 1,
            'LibChartEn' => 'Wario Colosseum',
            'libChartFr' => 'Wario Colosseum',
            'types'     => [3],
        ],
        [
            'id'   => 16,
            'group_id'   => 1,
            'LibChartEn' => 'Yoshi Circuit',
            'libChartFr' => 'Yoshi Circuit',
            'types'     => [2],
        ],
    ];

    private function updateGeneratorType(ObjectManager $manager): void
    {
        foreach ($this->entities as $entity) {
            $metadata = $manager->getClassMetaData("VideoGamesRecords\\CoreBundle\\Entity\\" . $entity);
            $metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_NONE);
        }
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $this->updateGeneratorType($manager);
        $this->loadTypes($manager);
        $this->loadCharts($manager);
        $manager->flush();
    }


    /**
     * @param ObjectManager $manager
     */
    private function loadTypes(ObjectManager $manager): void
    {
        foreach ($this->types as $row) {
            $chartType = new ChartType();
            $chartType->setId($row['id']);
            $chartType->setName($row['name']);
            $chartType->setMask($row['mask']);
            $chartType->setOrderBy($row['order_by']);

            $manager->persist($chartType);
            $this->addReference('chart-type.' . $chartType->getId(), $chartType);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadCharts(ObjectManager $manager): void
    {
        foreach ($this->charts as $row) {
            $chart = new Chart();
            $chart->setId($row['id']);
            $chart->setLibChartEn($row['LibChartEn']);
            $chart->setLibChartFr($row['libChartFr']);
            $chart->setCreatedAt(new \Datetime());
            $chart->setUpdatedAt(new \Datetime());
            $chart->setGroup($this->getReference('group' . $row['group_id']));

            foreach ($row['types'] as $type) {
                $chartLib = new ChartLib();
                $chartLib->setChart($chart);
                $chartLib->setType($this->getReference('chart-type.' . $type));
                $chartLib->setCreatedAt(new \Datetime());
                $chartLib->setUpdatedAt(new \Datetime());
                $chartLib->setName('test');

                $chart->addLib($chartLib);
            }

            $manager->persist($chart);
            $this->addReference('chart' . $chart->getId(), $chart);
        }
    }
}
