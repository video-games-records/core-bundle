<?php

namespace VideoGamesRecords\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
class LoadFixtures extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

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
        $list = array(
            array(
                'idSerie' => 1,
                'name' => 'Forza Motosport',
            ),
            array(
                'idSerie' => 2,
                'name' => 'Mario Kart',
            ),
        );

        foreach ($list as $row) {
            $serie = new Serie();
            $serie
                ->setIdSerie($row['idSerie'])
                ->setLibSerie($row['name']);
            $manager->persist($serie);
            $this->addReference('serie.' . $serie->getIdSerie(), $serie);
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
        $list = array(
            array(
                'idPlatform' => 1,
                'libPlatform' => 'Game Cube',
            ),
            array(
                'idPlatform' => 2,
                'libPlatform' => 'Playstation 2',
            ),
            array(
                'idPlatform' => 3,
                'libPlatform' => 'Xbox',
            ),
        );
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
        $list = array(
            array(
                'idGame' => 1,
                'languages' => ['fr' => 'Burnout 2', 'en' => 'Burnout 2'],
                'platforms' => array(1,2,3)
            ),
            array(
                'idGame' => 2,
                'languages' => ['fr' => 'Mario Kart 8', 'en' => 'Mario Kart 8'],
                'idSerie' => 2,
            ),
            array(
                'idGame' => 3,
                'languages' => ['fr' => 'Forza Motosport 4', 'en' => 'Forza Motosport 4'],
                'idSerie' => 1,
            ),
            array(
                'idGame' => 4,
                'languages' => ['fr' => 'Forza Motosport 3', 'en' => 'Forza Motosport 3'],
                'idSerie' => 1,
            ),
            array(
                'idGame' => 5,
                'languages' => ['fr' => 'Sega Rallye', 'en' => 'Sega Rallye'],
            ),
            array(
                'idGame' => 6,
                'languages' => ['fr' => 'Gran Turismo', 'en' => 'Gran Turismo'],
                'platforms' => array(2)
            ),
            array(
                'idGame' => 7,
                'languages' => ['fr' => 'Jet Set Radio', 'en' => 'Jet Set Radio'],
            ),
            array(
                'idGame' => 11,
                'languages' => ['fr' => 'Mario Kart Double Dash', 'en' => 'Mario Kart Double Dash'],
                'idSerie' => 2,
                'platforms' => array(1)
            ),
        );

        foreach ($list as $row) {
            $game = new Game();
            $game->setId($row['idGame']);
            foreach ($row['languages'] as $locale => $label) {
                $game->translate($locale)->setName($label);
            }
            if (isset($row['idSerie'])) {
                $game->setSerie($this->getReference('serie.' . $row['idSerie']));
            }
            if (isset($row['platforms'])) {
                foreach($row['platforms'] as $id) {
                    $game->addPlatform($this->getReference('platform.' . $id));
                }
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

        $list = array(
            array(
                'idGroup' => 1,
                'idGame' => 11,
                'libGroupEn' => 'Fastest Lap Times',
            ),
            array(
                'idGroup' => 2,
                'idGame' => 11,
                'libGroupEn' => 'Fastest Total Times',
            ),
            array(
                'idGroup' => 3,
                'idGame' => 11,
                'libGroupEn' => 'GP',
            ),
        );

        foreach ($list as $row) {
            $group = new Group();
            $group->setIdGroup($row['idGroup']);
            $group->setLibGroupEn($row['libGroupEn']);
            $group->setGame($this->getReference('game' . $row['idGame']));
            $manager->persist($group);
            $this->addReference('group' . $group->getIdGroup(), $group);
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

        $list = array(
            array(
                'idChart' => 1,
                'idGroup' => 1,
                'libChartEn' => 'Baby Park',
                'types' => [1],
            ),
            array(
                'idChart' => 2,
                'idGroup' => 1,
                'libChartEn' => 'Bowser\'s Castle',
                'types' => [1],
            ),
            array(
                'idChart' => 3,
                'idGroup' => 1,
                'libChartEn' => 'Daisy Cruiser',
                'types' => [2],
            ),
            array(
                'idChart' => 4,
                'idGroup' => 1,
                'libChartEn' => 'Dino Dino Jungle',
                'types' => [3],
            ),
            array(
                'idChart' => 5,
                'idGroup' => 1,
                'libChartEn' => 'DK Mountain',
                'types' => [1],
            ),
            array(
                'idChart' => 6,
                'idGroup' => 1,
                'libChartEn' => 'Dry Dry Desert',
                'types' => [2],
            ),
            array(
                'idChart' => 7,
                'idGroup' => 1,
                'libChartEn' => 'Luigi Circuit',
                'types' => [1, 2],
            ),
            array(
                'idChart' => 8,
                'idGroup' => 1,
                'libChartEn' => 'Mario Circuit',
                'types' => [1, 3],
            ),
            array(
                'idChart' => 9,
                'idGroup' => 1,
                'libChartEn' => 'Mushroom Bridge',
                'types' => [2, 3],
            ),
            array(
                'idChart' => 10,
                'idGroup' => 1,
                'libChartEn' => 'Mushroom City',
                'types' => [1],
            ),
            array(
                'idChart' => 11,
                'idGroup' => 1,
                'libChartEn' => 'Peach Beach',
                'types' => [1],
            ),
            array(
                'idChart' => 12,
                'idGroup' => 1,
                'libChartEn' => 'Rainbow Road',
                'types' => [1],
            ),
            array(
                'idChart' => 13,
                'idGroup' => 1,
                'libChartEn' => 'Sherbet Land',
                'types' => [1],
            ),
            array(
                'idChart' => 14,
                'idGroup' => 1,
                'libChartEn' => 'Waluigi Stadium',
                'types' => [1],
            ),
            array(
                'idChart' => 15,
                'idGroup' => 1,
                'libChartEn' => 'Wario Colosseum',
                'types' => [3],
            ),
            array(
                'idChart' => 16,
                'idGroup' => 1,
                'libChartEn' => 'Yoshi Circuit',
                'types' => [2],
            ),
        );

        foreach ($list as $row) {
            $chart = new Chart();
            $chart->setIdChart($row['idChart']);
            $chart->setLibChartEn($row['libChartEn']);
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
            $this->addReference('chart' . $chart->getIdChart(), $chart);
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

        $list = array(
            array(
                'idType' => 1,
                'name' => 'Score',
                'mask' => '30~',
                'orderBy' => 'DESC',
            ),
            array(
                'idType' => 2,
                'name' => 'Temps',
                'mask' => '30~:|2~.|2~',
                'orderBy' => 'ASC',
            ),
            array(
                'idType' => 3,
                'name' => 'Distance',
                'mask' => '30~ m',
                'orderBy' => 'DESC',
            ),
        );

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
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    private function loadPlayers(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Player');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $list = array(
            array(
                'idPlayer' => 1,
                'pseudo' => 'magicbart',
            ),
            array(
                'idPlayer' => 2,
                'pseudo' => 'kloh',
            ),
            array(
                'idPlayer' => 3,
                'pseudo' => 'viviengaetan',
            ),
        );

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
        // idChart = 1
        $chart = $this->getReference('chart1');

        $list = array(
            array(
                'idPlayer' => 1,
                'value' => 9999,
            ),
            array(
                'idPlayer' => 2,
                'value' => 10101,
            ),
            array(
                'idPlayer' => 3,
                'value' => 8900,
            ),
        );

        foreach ($list as $row) {
            $playerChart = new PlayerChart();
            $playerChart->setPlayer($this->getReference('player' . $row['idPlayer']));
            $playerChart->setChart($chart);
            $playerChart->setIdStatus(1);
            $playerChart->setDateModif(new \DateTime());
            $manager->persist($playerChart);

            foreach ($chart->getLibs() as $lib) {
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


}
