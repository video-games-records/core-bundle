<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataFixtures;

use Datetime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ObjectManager;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartLib;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;

class PlayerChartFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            ChartFixtures::class
        ];
    }

    /**
     * @var array<string>
     */
    private array $entities = [
        'PlayerChartStatus', 'PlayerChart'
    ];


    /**
     * @var array<mixed>
     */
    private array $status = [
        [
            'id' => 1,
            'libStatus' => 'NORMAL',
            'class'     => 'proof--none',
            'ranking'   => true,
            'proof'     => false,
        ],
        [
            'id' => 2,
            'libStatus' => 'DEMAND',
            'class'     => 'proof--request-pending',
            'ranking'   => true,
            'proof'     => false,
        ],
        [
            'id' => 3,
            'libStatus' => 'INVESTIGATION',
            'class'     => 'proof--request-validated',
            'ranking'   => false,
            'proof'     => false,
        ],
        [
            'id' => 4,
            'libStatus' => 'DEMAND_SEND_PROOF',
            'class'     => 'proof--request-sent',
            'ranking'   => true,
            'proof'     => true,
        ],
        [
            'id' => 5,
            'libStatus' => 'NORMAL_SEND_PROOF',
            'class'     => 'proof--sent',
            'ranking'   => true,
            'proof'     => true,
        ],
        [
            'id' => 6,
            'libStatus' => 'PROOVED',
            'class'     => 'proof--proved',
            'ranking'   => true,
            'proof'     => true,
        ],
        [
            'id' => 7,
            'libStatus' => 'NOT_PROOVED',
            'class'     => 'proof--unproved',
            'ranking'   => true,
            'proof'     => false,
        ],
    ];

    /**
     * @var array<mixed>
     */
    private array $scores = [
        [
            'player_id' => 1,
            'value'    => 9999,
            'status'   => 1,
        ],
        [
            'player_id' => 2,
            'value'    => 10101,
            'status'   => 6,
        ],
        [
            'player_id' => 3,
            'value'    => 10101,
            'status'   => 2,
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
        $this->loadStatus($manager);
        $this->loadScores($manager);
        $manager->flush();
    }


    /**
     * @param $manager
     */
    public function loadStatus($manager): void
    {
        foreach ($this->status as $row) {
            $playerChartStatus = new PlayerChartStatus();
            $playerChartStatus->setId($row['id']);
            $playerChartStatus->setName($row['libStatus']);
            $playerChartStatus->setClass($row['class']);
            $playerChartStatus->setBoolRanking($row['ranking']);
            $playerChartStatus->setBoolSendProof($row['proof']);

            $manager->persist($playerChartStatus);
            $this->addReference('player-chart-status' . $playerChartStatus->getId(), $playerChartStatus);
        }
    }


    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    private function loadScores(ObjectManager $manager): void
    {
        /** @var Chart $chart */
        $chart = $this->getReference('chart1');

        foreach ($this->scores as $row) {
            $playerChart = new PlayerChart();
            $playerChart->setPlayer($this->getReference('player' . $row['player_id']));
            $playerChart->setChart($chart);
            $playerChart->setStatus($this->getReference(sprintf('player-chart-status%d', $row['status'])));
            $playerChart->setCreatedAt(new Datetime());
            $playerChart->setUpdatedAt(new Datetime());
            $playerChart->setLastUpdate(new DateTime());
            $manager->persist($playerChart);

            foreach ($chart->getLibs() as $lib) {
                $playerChartLib = new PlayerChartLib();
                $playerChartLib->setPlayerChart($playerChart);
                $playerChartLib->setLibChart($lib);
                $playerChartLib->setValue($row['value']);
                $manager->persist($playerChartLib);
            }
        }
        $chart->setNbPost(count($this->scores));
    }
}
