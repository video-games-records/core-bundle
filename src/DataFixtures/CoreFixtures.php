<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CoreFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            PlayerStatusFixtures::class,
            SerieFixtures::class,
            GameFixtures::class,
            GroupFixtures::class,
            ChartFixtures::class,
            PlayerChartFixtures::class,
            PlayerFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $manager->flush();
    }
}
