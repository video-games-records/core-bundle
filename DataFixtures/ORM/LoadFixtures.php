<?php
namespace VideoGamesRecords\CoreBundle\DataFixtures\ORM;

use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Entity\Serie;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

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
class LoadFixtures implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->loadSeries($manager);
        $this->loadGames($manager);
        $this->loadGroups($manager);
        $this->loadCharts($manager);
    }


    /**
     * @param ObjectManager $manager
     */
    private function loadSeries(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Serie');
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $serie = new Serie();
        $serie->setIdSerie(1);
        $serie->setLibSerie('Forza Motosport');
        $manager->persist($serie);
        $serie = new Serie();
        $serie->setIdSerie(2);
        $serie->setLibSerie('Mario Kart');
        $manager->persist($serie);
        $manager->flush();
    }


    /**
     * @param ObjectManager $manager
     */
    private function loadGames(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Game');
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $list = array(
            array(
                'idGame' => 1,
                'libGameEn' => 'Burnout 2',
            ),
            array(
                'idGame' => 2,
                'libGameEn' => 'Mario Kart 8',
                'idSerie' => 2,
            ),
            array(
                'idGame' => 3,
                'libGameEn' => 'Forza Motosport 4',
                'idSerie' => 1,
            ),
            array(
                'idGame' => 4,
                'libGameEn' => 'Forza Motosport 3',
                'idSerie' => 1,
            ),
            array(
                'idGame' => 5,
                'libGameEn' => 'Sega Rallye',
            ),
            array(
                'idGame' => 6,
                'libGameEn' => 'Gran Turismo',
            ),
            array(
                'idGame' => 7,
                'libGameEn' => 'Jet Set Radio',
            ),
            array(
                'idGame' => 11,
                'libGameEn' => 'Mario Kart Double Dash',
                'idSerie' => 2,
            ),
        );

        foreach ($list as $row) {
            $game = new Game();
            $game->setIdGame($row['idGame']);
            $game->setLibGameEn($row['libGameEn']);
            if (isset($row['idSerie'])) {
                $serie = $manager->getReference('VideoGamesRecords\CoreBundle\Entity\Serie', $row['idSerie']);
                $game->setSerie($serie);
            }
            $manager->persist($game);
        }

        $manager->flush();
    }


    /**
     * @param ObjectManager $manager
     */
    private function loadGroups(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Group');
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

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
            $game = $manager->getReference('VideoGamesRecords\CoreBundle\Entity\Game', $row['idGame']);
            $group->setGame($game);
            $manager->persist($group);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadCharts(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetaData('VideoGamesRecords\CoreBundle\Entity\Chart');
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

        $list = array(
            array(
                'idChart' => 1,
                'idGroup' => 1,
                'libChartEn' => 'Baby Park',
            ),
            array(
                'idChart' => 2,
                'idGroup' => 1,
                'libChartEn' => 'Bowser\'s Castle',
            ),
            array(
                'idChart' => 3,
                'idGroup' => 1,
                'libChartEn' => 'Daisy Cruiser',
            ),
            array(
                'idChart' => 4,
                'idGroup' => 1,
                'libChartEn' => 'Dino Dino Jungle',
            ),
            array(
                'idChart' => 5,
                'idGroup' => 1,
                'libChartEn' => 'DK Mountain',
            ),
            array(
                'idChart' => 6,
                'idGroup' => 1,
                'libChartEn' => 'Dry Dry Desert',
            ),
            array(
                'idChart' => 7,
                'idGroup' => 1,
                'libChartEn' => 'Luigi Circuit',
            ),
            array(
                'idChart' => 8,
                'idGroup' => 1,
                'libChartEn' => 'Mario Circuit',
            ),
            array(
                'idChart' => 9,
                'idGroup' => 1,
                'libChartEn' => 'Mushroom Bridge',
            ),
            array(
                'idChart' => 10,
                'idGroup' => 1,
                'libChartEn' => 'Mushroom City',
            ),
            array(
                'idChart' => 11,
                'idGroup' => 1,
                'libChartEn' => 'Peach Beach',
            ),
            array(
                'idChart' => 12,
                'idGroup' => 1,
                'libChartEn' => 'Rainbow Road',
            ),
            array(
                'idChart' => 13,
                'idGroup' => 1,
                'libChartEn' => 'Sherbet Land',
            ),
            array(
                'idChart' => 14,
                'idGroup' => 1,
                'libChartEn' => 'Waluigi Stadium',
            ),
            array(
                'idChart' => 15,
                'idGroup' => 1,
                'libChartEn' => 'Wario Colosseum',
            ),
            array(
                'idChart' => 16,
                'idGroup' => 1,
                'libChartEn' => 'Yoshi Circuit',
            ),
        );

        foreach ($list as $row) {
            $chart = new Chart();
            $chart->setIdChart($row['idChart']);
            $chart->setLibChartEn($row['libChartEn']);
            $group = $manager->getReference('VideoGamesRecords\CoreBundle\Entity\Group', $row['idGroup']);
            $chart->setGroup($group);
            $manager->persist($chart);
        }

        $manager->flush();
    }
}
