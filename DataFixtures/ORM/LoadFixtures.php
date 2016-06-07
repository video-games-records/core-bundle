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
    }


    /**
     * @param ObjectManager $manager
     */
    private function loadSeries(ObjectManager $manager)
    {
        $serie = new Serie();
        $serie->setLibSerie('Forza Motosport');
        $manager->persist($serie);
        $serie = new Serie();
        $serie->setLibSerie('Mario Kart');
        $manager->persist($serie);
        $manager->flush();
    }


    /**
     * @param ObjectManager $manager
     */
    private function loadGames(ObjectManager $manager)
    {
        $list = array(
            array(
                'libGameEn' => 'Burnout 2',
            ),
            array(
                'libGameEn' => 'Mario Kart 8',
                'idSerie' => 2,
            ),
            array(
                'libGameEn' => 'Forza Motosport 4',
                'idSerie' => 1,
            ),
            array(
                'libGameEn' => 'Forza Motosport 3',
                'idSerie' => 1,
            ),
            array(
                'libGameEn' => 'Sega Rallye',
            ),
            array(
                'libGameEn' => 'Gran Turismo',
            ),
            array(
                'libGameEn' => 'Jet Set Radio',
            ),
            array(
                'libGameEn' => 'Mario Kart Double Dash',
                'idSerie' => 2,
            ),
        );

        foreach ($list as $row) {
            $game = new Game();
            $game->setLibGameEn($row['libGameEn']);
            if (isset($row['idSerie'])) {
                $serie = $manager->getReference('VideoGamesRecords\CoreBundle\Entity\Serie', $row['idSerie']);
                $game->setSerie($serie);
            }
            $manager->persist($game);
        }

        $manager->flush();
    }


}
