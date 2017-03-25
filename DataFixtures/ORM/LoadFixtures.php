<?php

namespace VideoGamesRecords\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;
use VideoGamesRecords\CoreBundle\Entity\ChartType;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Entity\Serie;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartLib;

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
class LoadFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->loadSeries($manager);
        $this->loadPlatforms($manager);
        $this->loadGames($manager);
        $this->loadGroups($manager);
        $this->loadChartType($manager);
        $this->loadCharts($manager);
        $this->loadPlayers($manager);
        $this->loadPlayerChart($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadSeries(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Serie');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $list = [
            [
                'id' => 1,
                'languages' => ['fr' => 'Forza Motosport', 'en' => 'Forza Motosport']
            ],
            [
                'id' => 2,
                'languages' => ['fr' => 'Mario Kart', 'en' => 'Mario Kart']
            ],
        ];

        foreach ($list as $row) {
            $serie = new Serie();
            $serie->setId($row['id']);
            foreach ($row['languages'] as $locale => $label) {
                $serie->translate($locale, false)->setName($label);
            }
            $serie->mergeNewTranslations();
            $manager->persist($serie);
            $this->addReference('serie.' . $serie->getId(), $serie);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadPlatforms(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Platform');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $list = [
            [
                'idPlatform' => 1,
                'libPlatform' => 'Game Cube',
            ],
            [
                'idPlatform' => 2,
                'libPlatform' => 'Playstation 2',
            ],
            [
                'idPlatform' => 3,
                'libPlatform' => 'Xbox',
            ],
        ];
        foreach ($list as $row) {
            $platform = new Platform();
            $platform->setIdPlatform($row['idPlatform']);
            $platform->setLibPlatform($row['libPlatform']);
            $manager->persist($platform);
            $this->addReference('platform.' . $platform->getIdPlatform(), $platform);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadGames(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Game');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $list = [
            [
                'idGame' => 1,
                'languages' => ['fr' => 'Burnout 2', 'en' => 'Burnout 2'],
                'platforms' => [1, 2, 3],
                'status' => Game::STATUS_ACTIVE,
            ],
            [
                'idGame' => 2,
                'languages' => ['fr' => 'Mario Kart 8', 'en' => 'Mario Kart 8'],
                'idSerie' => 2,
            ],
            [
                'idGame' => 3,
                'languages' => ['fr' => 'Forza Motosport 4', 'en' => 'Forza Motosport 4'],
                'idSerie' => 1,
            ],
            [
                'idGame' => 4,
                'languages' => ['fr' => 'Forza Motosport 3', 'en' => 'Forza Motosport 3'],
                'idSerie' => 1,
            ],
            [
                'idGame' => 5,
                'languages' => ['fr' => 'Sega Rallye', 'en' => 'Sega Rallye'],
            ],
            [
                'idGame' => 6,
                'languages' => ['fr' => 'Gran Turismo', 'en' => 'Gran Turismo'],
                'platforms' => [2],
            ],
            [
                'idGame' => 7,
                'languages' => ['fr' => 'Jet Set Radio', 'en' => 'Jet Set Radio'],
            ],
            [
                'idGame' => 11,
                'languages' => ['fr' => 'Mario Kart Double Dash', 'en' => 'Mario Kart Double Dash'],
                'idSerie' => 2,
                'platforms' => [1],
                'status' => Game::STATUS_ACTIVE,
            ],
        ];

        foreach ($list as $row) {
            $game = new Game();
            $game->setId($row['idGame']);
            foreach ($row['languages'] as $locale => $label) {
                $game->translate($locale, false)->setName($label);
            }
            if (isset($row['idSerie'])) {
                $game->setSerie($this->getReference('serie.' . $row['idSerie']));
            }
            if (isset($row['platforms'])) {
                foreach ($row['platforms'] as $id) {
                    $game->addPlatform($this->getReference('platform.' . $id));
                }
            }
            if (isset($row['status']) && Game::STATUS_ACTIVE === $row['status']) {
                $game->setStatus(Game::STATUS_ACTIVE);
            }
            $game->mergeNewTranslations();
            $manager->persist($game);
            $this->addReference('game' . $game->getId(), $game);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadGroups(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Group');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $list = [
            [
                'idGroup' => 1,
                'idGame' => 11,
                'languages' => ['fr' => 'Meilleur Tour', 'en' => 'Fastest Lap Times'],
            ],
            [
                'idGroup' => 2,
                'idGame' => 11,
                'languages' => ['fr' => 'Meilleur Temps', 'en' => 'Fastest Total Times'],
            ],
            [
                'idGroup' => 3,
                'idGame' => 11,
                'languages' => ['fr' => 'Grand Prix', 'en' => 'GP'],
            ],
        ];

        foreach ($list as $row) {
            $group = new Group();
            $group->setId($row['idGroup']);
            foreach ($row['languages'] as $locale => $label) {
                $group->translate($locale, false)->setName($label);
            }
            $group->setGame($this->getReference('game' . $row['idGame']));
            $group->mergeNewTranslations();
            $manager->persist($group);
            $this->addReference('group' . $group->getId(), $group);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadCharts(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Chart');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $list = [
            [
                'idChart' => 1,
                'idGroup' => 1,
                'languages' => ['fr' => 'Baby Park', 'en' => 'Baby Park'],
                'types' => [1],
            ],
            [
                'idChart' => 2,
                'idGroup' => 1,
                'languages' => ['fr' => 'Bowser\'s Castle', 'en' => 'Bowser\'s Castle'],
                'types' => [1],
            ],
            [
                'idChart' => 3,
                'idGroup' => 1,
                'languages' => ['fr' => 'Daisy Cruiser', 'en' => 'Daisy Cruiser'],
                'types' => [2],
            ],
            [
                'idChart' => 4,
                'idGroup' => 1,
                'languages' => ['fr' => 'Dino Dino Jungle', 'en' => 'Dino Dino Jungle'],
                'types' => [3],
            ],
            [
                'idChart' => 5,
                'idGroup' => 1,
                'languages' => ['fr' => 'DK Mountain', 'en' => 'DK Mountain'],
                'types' => [1],
            ],
            [
                'idChart' => 6,
                'idGroup' => 1,
                'languages' => ['fr' => 'Dry Dry Desert', 'en' => 'Dry Dry Desert'],
                'types' => [2],
            ],
            [
                'idChart' => 7,
                'idGroup' => 1,
                'languages' => ['fr' => 'Luigi Circuit', 'en' => 'Luigi Circuit'],
                'types' => [1, 2],
            ],
            [
                'idChart' => 8,
                'idGroup' => 1,
                'languages' => ['fr' => 'Mario Circuit', 'en' => 'Mario Circuit'],
                'types' => [1, 3],
            ],
            [
                'idChart' => 9,
                'idGroup' => 1,
                'languages' => ['fr' => 'Mushroom Bridge', 'en' => 'Mushroom Bridge'],
                'types' => [2, 3],
            ],
            [
                'idChart' => 10,
                'idGroup' => 1,
                'languages' => ['fr' => 'Mushroom City', 'en' => 'Mushroom City'],
                'types' => [1],
            ],
            [
                'idChart' => 11,
                'idGroup' => 1,
                'languages' => ['fr' => 'Peach Beach', 'en' => 'Peach Beach'],
                'types' => [1],
            ],
            [
                'idChart' => 12,
                'idGroup' => 1,
                'languages' => ['fr' => 'Rainbow Road', 'en' => 'Rainbow Road'],
                'types' => [1],
            ],
            [
                'idChart' => 13,
                'idGroup' => 1,
                'languages' => ['fr' => 'Sherbet Land', 'en' => 'Sherbet Land'],
                'types' => [1],
            ],
            [
                'idChart' => 14,
                'idGroup' => 1,
                'languages' => ['fr' => 'Waluigi Stadium', 'en' => 'Waluigi Stadium'],
                'types' => [1],
            ],
            [
                'idChart' => 15,
                'idGroup' => 1,
                'languages' => ['fr' => 'Wario Colosseum', 'en' => 'Wario Colosseum'],
                'types' => [3],
            ],
            [
                'idChart' => 16,
                'idGroup' => 1,
                'languages' => ['fr' => 'Yoshi Circuit', 'en' => 'Yoshi Circuit'],
                'types' => [2],
            ],
        ];

        foreach ($list as $row) {
            $chart = new Chart();
            $chart->setId($row['idChart']);
            foreach ($row['languages'] as $locale => $label) {
                $chart->translate($locale, false)->setName($label);
            }
            $chart->setGroup($this->getReference('group' . $row['idGroup']));
            $chart->mergeNewTranslations();

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
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    private function loadChartType(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\ChartType');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $list = [
            [
                'idType' => 1,
                'name' => 'Score',
                'mask' => '30~',
                'orderBy' => 'DESC',
            ],
            [
                'idType' => 2,
                'name' => 'Temps',
                'mask' => '30~:|2~.|2~',
                'orderBy' => 'ASC',
            ],
            [
                'idType' => 3,
                'name' => 'Distance',
                'mask' => '30~ m',
                'orderBy' => 'DESC',
            ],
        ];

        foreach ($list as $row) {
            /** @var \VideoGamesRecords\CoreBundle\Entity\ChartType $chartType */
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
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    private function loadPlayers(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Player');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $list = [
            [
                'idPlayer' => 1,
                'pseudo' => 'magicbart',
            ],
            [
                'idPlayer' => 2,
                'pseudo' => 'kloh',
            ],
            [
                'idPlayer' => 3,
                'pseudo' => 'viviengaetan',
            ],
        ];

        foreach ($list as $row) {
            $player = new Player();
            $player
                ->setIdPlayer($row['idPlayer'])
                ->setPseudo($row['pseudo']);

            $manager->persist($player);
            $this->addReference('player' . $player->getIdPlayer(), $player);
        }
        $manager->flush();
    }


    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    private function loadPlayerChart(ObjectManager $manager)
    {
        /** @var \VideoGamesRecords\CoreBundle\Entity\Chart $chart */
        $chart = $this->getReference('chart1');

        $list = [
            [
                'idPlayer' => 1,
                'value' => 9999,
            ],
            [
                'idPlayer' => 2,
                'value' => 10101,
            ],
            [
                'idPlayer' => 3,
                'value' => 8900,
            ],
        ];

        foreach ($list as $row) {
            /** @var \VideoGamesRecords\CoreBundle\Entity\PlayerChart $playerChart */
            $playerChart = new PlayerChart();
            $playerChart->setPlayer($this->getReference('player' . $row['idPlayer']));
            $playerChart->setChart($chart);
            $playerChart->setIdStatus(1);
            $playerChart->setDateModif(new \DateTime());
            $manager->persist($playerChart);

            foreach ($chart->getLibs() as $lib) {
                /** @var \VideoGamesRecords\CoreBundle\Entity\PlayerChartLib $playerChartLib */
                $playerChartLib = new PlayerChartLib();
                $playerChartLib->setPlayer($this->getReference('player' . $row['idPlayer']));
                $playerChartLib->setLibChart($lib);
                $playerChartLib->setValue($row['value']);
                $manager->persist($playerChartLib);
            }
        }

        $manager->flush();

        $this->container->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->maj(1);
        $this->container->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->maj(1);
        $this->container->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->maj(11);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 50;
    }
}
