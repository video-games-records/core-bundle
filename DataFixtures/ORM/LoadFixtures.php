<?php
namespace VideoGamesRecords\CoreBundle\DataFixtures\ORM;

use VideoGamesRecords\CoreBundle\Entity\Game;
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
        $this->loadGames($manager);
    }

    private function loadGames(ObjectManager $manager)
    {
        $list = array(
            array(
                'libJeuEn' => 'Burnout 2',
            ),
            array(
                'libJeuEn' => 'Mario Kart 8',
            ),
            array(
                'libJeuEn' => 'Forza Motosport 4',
            ),
            array(
                'libJeuEn' => 'Forza Motosport 3',
            ),
            array(
                'libJeuEn' => 'Sega Rallye',
            ),
            array(
                'libJeuEn' => 'Gran Turismo',
            ),
            array(
                'libJeuEn' => 'Jet Set Radio',
            ),
            array(
                'libJeuEn' => 'Burnout Paradise',
            ),
        );

        foreach ($list as $row) {
            $game = new Game();
            $game->setLibGameEn($row['libJeuEn']);
            $game->setLibGameFr(isset($row['libJeuFr']) ? $row['libJeuFr'] : $row['libJeuEn']);
            $manager->persist($game);
        }

        $manager->flush();
    }


}
