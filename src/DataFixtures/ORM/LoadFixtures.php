<?php

namespace VideoGamesRecords\CoreBundle\DataFixtures\ORM;

use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;
use VideoGamesRecords\CoreBundle\Entity\ChartType;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartLib;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\Serie;
use VideoGamesRecords\CoreBundle\ValueObject\GameStatus;

/**
 * Defines the sample data to load in the database when running the unit and
 * functional tests. Execute this command to load the data:
 *
 *   $ php app/console doctrine:fixtures:load
 *
 * See http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
 *
 * @author David Benard <magicbart@gmail.com>
 */
class LoadFixtures extends Fixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadPlayerChartStatus($manager);
        $this->loadPlayers($manager);
        $this->loadChartType($manager);
        $this->loadSeries($manager);
        $this->loadPlatforms($manager);
        $this->loadGames($manager);
        $this->loadGroups($manager);
        $this->loadCharts($manager);
        $this->loadPlayerChart($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadSeries(ObjectManager $manager): void
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Serie');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $list = [
            [
                'id'        => 1,
                'libSerie' => 'Forza Motosport',
            ],
            [
                'id'        => 2,
                'libSerie' => 'Mario Kart',
            ],
        ];

        foreach ($list as $row) {
            $serie = new Serie();
            $serie->setId($row['id']);
            $serie->setLibSerie($row['libSerie']);
            $manager->persist($serie);
            $this->addReference('serie.' . $serie->getId(), $serie);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadPlatforms(ObjectManager $manager): void
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Platform');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $list = [
            [
                'idPlatform'  => 1,
                'libPlatform' => 'Game Cube',
            ],
            [
                'idPlatform'  => 2,
                'libPlatform' => 'Playstation 2',
            ],
            [
                'idPlatform'  => 3,
                'libPlatform' => 'Xbox',
            ],
        ];
        foreach ($list as $row) {
            $platform = new Platform();
            $platform->setId($row['idPlatform']);
            $platform->setLibPlatform($row['libPlatform']);
            $manager->persist($platform);
            $this->addReference('platform.' . $platform->getId(), $platform);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadGames(ObjectManager $manager): void
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Game');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $list = [
            [
                'idGame'    => 1,
                'LibGameEn' => 'Burnout 2',
                'libGameFr' => 'Burnout 2',
                'platforms' => [1, 2, 3],
                'status'    => GameStatus::STATUS_ACTIVE,
            ],
            [
                'idGame'    => 2,
                'LibGameEn' => 'Mario Kart 8',
                'libGameFr' => 'Mario Kart 8',
                'idSerie'   => 2,
            ],
            [
                'idGame'    => 3,
                'LibGameEn' => 'Forza Motosport 4',
                'libGameFr' => 'Forza Motosport 4',
                'idSerie'   => 1,
            ],
            [
                'idGame'    => 4,
                'LibGameEn' => 'Forza Motosport 3',
                'libGameFr' => 'Forza Motosport 3',
                'idSerie'   => 1,
            ],
            [
                'idGame'    => 5,
                'LibGameEn' => 'Sega Rallye [EN]',
                'libGameFr' => 'Sega Rallye [FR]',
            ],
            [
                'idGame'    => 6,
                'LibGameEn' => 'Gran Turismo',
                'libGameFr' => 'Gran Turismo',
                'platforms' => [2],
            ],
            [
                'idGame'    => 7,
                'LibGameEn' => 'Jet Set Radio',
                'libGameFr' => 'Jet Set Radio',
            ],
            [
                'idGame'    => 11,
                'LibGameEn' => 'Mario Kart Double Dash',
                'libGameFr' => 'Mario Kart Double Dash',
                'idSerie'   => 2,
                'platforms' => [1],
                'status'    => GameStatus::STATUS_ACTIVE,
            ],
        ];

        foreach ($list as $row) {
            $game = new Game();
            $game->setId($row['idGame']);
            $game->setLibGameEn($row['LibGameEn']);
            $game->setLibGameFr($row['libGameFr']);

            if (isset($row['idSerie'])) {
                $game->setSerie($this->getReference('serie.' . $row['idSerie']));
            }
            if (isset($row['platforms'])) {
                foreach ($row['platforms'] as $id) {
                    $game->addPlatform($this->getReference('platform.' . $id));
                }
            }
            if (isset($row['status']) && GameStatus::STATUS_ACTIVE === $row['status']) {
                $game->setStatus(GameStatus::STATUS_ACTIVE);
            }
            $manager->persist($game);
            $this->addReference('game' . $game->getId(), $game);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadGroups(ObjectManager $manager): void
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Group');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $list = [
            [
                'idGroup'   => 1,
                'idGame'    => 11,
                'LibGroupEn' => 'Meilleur Tour',
                'libGroupFr' => 'Fastest Lap Times',
            ],
            [
                'idGroup'   => 2,
                'idGame'    => 11,
                'LibGroupEn' => 'Meilleur Temps',
                'libGroupFr' => 'Fastest Total Times',
            ],
            [
                'idGroup'   => 3,
                'idGame'    => 11,
                'LibGroupEn' => 'Grand Prix',
                'libGroupFr' => 'Grand Prix',
            ],
        ];

        foreach ($list as $row) {
            $group = new Group();
            $group->setId($row['idGroup']);
            $group->setLibGroupEn($row['LibGroupEn']);
            $group->setLibGroupFr($row['libGroupFr']);
            $group->setGame($this->getReference('game' . $row['idGame']));
            $manager->persist($group);
            $this->addReference('group' . $group->getId(), $group);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadChartType(ObjectManager $manager): void
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\ChartType');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $list = [
            [
                'idType'  => 1,
                'name'    => 'Score',
                'mask'    => '30~',
                'orderBy' => 'DESC',
            ],
            [
                'idType'  => 2,
                'name'    => 'Temps',
                'mask'    => '30~:|2~.|2~',
                'orderBy' => 'ASC',
            ],
            [
                'idType'  => 3,
                'name'    => 'Distance',
                'mask'    => '30~ m',
                'orderBy' => 'DESC',
            ],
        ];

        foreach ($list as $row) {
            $chartType = new ChartType();
            $chartType
                ->setIdType($row['idType'])
                ->setName($row['name'])
                ->setMask($row['mask'])
                ->setOrderBy($row['orderBy']);

            $manager->persist($chartType);
            $this->addReference('charttype.' . $chartType->getIdType(), $chartType);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadCharts(ObjectManager $manager): void
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Chart');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $list = [
            [
                'idChart'   => 1,
                'idGroup'   => 1,
                'LibChartEn' => 'Baby Park',
                'libChartFr' => 'Baby Park',
                'types'     => [1],
            ],
            [
                'idChart'   => 2,
                'idGroup'   => 1,
                'LibChartEn' => 'Bowser\'s Castle',
                'libChartFr' => 'Bowser\'s Castle',
                'types'     => [1],
            ],
            [
                'idChart'   => 3,
                'idGroup'   => 1,
                'LibChartEn' => 'Daisy Cruiser',
                'libChartFr' => 'Daisy Cruiser',
                'types'     => [2],
            ],
            [
                'idChart'   => 4,
                'idGroup'   => 1,
                'LibChartEn' => 'Dino Dino Jungle',
                'libChartFr' => 'Dino Dino Jungle',
                'types'     => [3],
            ],
            [
                'idChart'   => 5,
                'idGroup'   => 1,
                'LibChartEn' => 'DK Mountain',
                'libChartFr' => 'DK Mountain',
                'types'     => [1],
            ],
            [
                'idChart'   => 6,
                'idGroup'   => 1,
                'LibChartEn' => 'Dry Dry Desert',
                'libChartFr' => 'Dry Dry Desert',
                'types'     => [2],
            ],
            [
                'idChart'   => 7,
                'idGroup'   => 1,
                'LibChartEn' => 'Luigi Circuit',
                'libChartFr' => 'Luigi Circuit',
                'types'     => [1, 2],
            ],
            [
                'idChart'   => 8,
                'idGroup'   => 1,
                'LibChartEn' => 'Mario Circuit',
                'libChartFr' => 'Mario Circuit',
                'types'     => [1, 3],
            ],
            [
                'idChart'   => 9,
                'idGroup'   => 1,
                'LibChartEn' => 'Mushroom Bridge',
                'libChartFr' => 'Mushroom Bridge',
                'types'     => [2, 3],
            ],
            [
                'idChart'   => 10,
                'idGroup'   => 1,
                'LibChartEn' => 'Mushroom City',
                'libChartFr' => 'Mushroom City',
                'types'     => [1],
            ],
            [
                'idChart'   => 11,
                'idGroup'   => 1,
                'LibChartEn' => 'Peach Beach',
                'libChartFr' => 'Peach Beach',
                'types'     => [1],
            ],
            [
                'idChart'   => 12,
                'idGroup'   => 1,
                'LibChartEn' => 'Rainbow Road',
                'libChartFr' => 'Rainbow Road',
                'types'     => [1],
            ],
            [
                'idChart'   => 13,
                'idGroup'   => 1,
                'LibChartEn' => 'Sherbet Land',
                'libChartFr' => 'Sherbet Land',
                'types'     => [1],
            ],
            [
                'idChart'   => 14,
                'idGroup'   => 1,
                'LibChartEn' => 'Waluigi Stadium',
                'libChartFr' => 'Waluigi Stadium',
                'types'     => [1],
            ],
            [
                'idChart'   => 15,
                'idGroup'   => 1,
                'LibChartEn' => 'Wario Colosseum',
                'libChartFr' => 'Wario Colosseum',
                'types'     => [3],
            ],
            [
                'idChart'   => 16,
                'idGroup'   => 1,
                'LibChartEn' => 'Yoshi Circuit',
                'libChartFr' => 'Yoshi Circuit',
                'types'     => [2],
            ],
        ];

        foreach ($list as $row) {
            $chart = new Chart();
            $chart->setId($row['idChart']);
            $chart->setLibChartEn($row['LibChartEn']);
            $chart->setLibChartFr($row['libChartFr']);
            $chart->setGroup($this->getReference('group' . $row['idGroup']));

            foreach ($row['types'] as $type) {
                $chartLib = new ChartLib();
                $chartLib
                    ->setChart($chart)
                    ->setType($this->getReference('charttype.' . $type))
                    ->setName('test');

                $chart->addLib($chartLib);
            }

            $manager->persist($chart);
            $this->addReference('chart' . $chart->getId(), $chart);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadPlayers(ObjectManager $manager): void
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Player');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $list = [
            [
                'idPlayer' => 1,
                'pseudo'   => 'magicbart',
            ],
            [
                'idPlayer' => 2,
                'pseudo'   => 'kloh',
            ],
            [
                'idPlayer' => 3,
                'pseudo'   => 'flatine',
            ],
        ];

        foreach ($list as $row) {
            $player = new Player();
            $player
                ->setId($row['idPlayer'])
                ->setPseudo($row['pseudo']);

            $manager->persist($player);
            $this->addReference('player' . $player->getId(), $player);
        }
        $manager->flush();
    }

    /**
     * @param $manager
     */
    public function loadPlayerChartStatus($manager): void
    {
        $list = [
            [
                'libStatus' => 'NORMAL',
                'class'     => 'proof--none',
                'ranking'   => true,
                'proof'     => false,
            ],
            [
                'libStatus' => 'DEMAND',
                'class'     => 'proof--request-pending',
                'ranking'   => true,
                'proof'     => false,
            ],
            [
                'libStatus' => 'INVESTIGATION',
                'class'     => 'proof--request-validated',
                'ranking'   => false,
                'proof'     => false,
            ],
            [
                'libStatus' => 'DEMAND_SEND_PROOF',
                'class'     => 'proof--request-sent',
                'ranking'   => true,
                'proof'     => true,
            ],
            [
                'libStatus' => 'NORMAL_SEND_PROOF',
                'class'     => 'proof--sent',
                'ranking'   => true,
                'proof'     => true,
            ],
            [
                'libStatus' => 'PROOVED',
                'class'     => 'proof--proved',
                'ranking'   => true,
                'proof'     => true,
            ],
            [
                'libStatus' => 'NOT_PROOVED',
                'class'     => 'proof--unproved',
                'ranking'   => true,
                'proof'     => false,
            ],
        ];

        foreach ($list as $key => $row) {
            $playerChartStatus = new PlayerChartStatus();
            $playerChartStatus
                ->setId($key + 1)
                ->setName($row['libStatus'])
                ->setClass($row['class'])
                ->setBoolRanking($row['ranking'])
                ->setBoolSendProof($row['proof']);

            $manager->persist($playerChartStatus);
            $this->addReference('playerchartstatus' . $playerChartStatus->getId(), $playerChartStatus);
        }
        $manager->flush();
    }


    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    private function loadPlayerChart(ObjectManager $manager): void
    {
        /** @var Chart $chart */
        $chart = $this->getReference('chart1');

        $list = [
            [
                'idPlayer' => 1,
                'value'    => 9999,
                'status'   => 1,
            ],
            [
                'idPlayer' => 2,
                'value'    => 10101,
                'status'   => 6,
            ],
            [
                'idPlayer' => 3,
                'value'    => 10101,
                'status'   => 2,
            ],
        ];

        foreach ($list as $row) {
            $playerChart = new PlayerChart();
            $playerChart->setPlayer($this->getReference('player' . $row['idPlayer']));
            $playerChart->setChart($chart);
            $playerChart->setStatus($this->getReference(sprintf('playerchartstatus%d', $row['status'])));
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
        $chart->setNbPost(count($list));

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder(): int
    {
        return 50;
    }
}
