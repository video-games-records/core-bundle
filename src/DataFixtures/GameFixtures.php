<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ObjectManager;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\ValueObject\GameStatus;

class GameFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            SerieFixtures::class
        ];
    }

    /**
     * @var array<string>
     */
    private array $entities = [
        'Platform', 'Game',
    ];


    /**
     * @var array<mixed>
     */
    private array $platforms = [
        [
            'id'  => 1,
            'name' => 'Game Cube',
        ],
        [
            'id'  => 2,
            'name' => 'Playstation 2',
        ],
        [
            'id'  => 3,
            'name' => 'Xbox',
        ],
    ];

    /**
     * @var array<mixed>
     */
    private array $games = [
        [
            'id'    => 1,
            'status'    => GameStatus::ACTIVE,
            'LibGameEn' => 'Burnout 2',
            'libGameFr' => 'Burnout 2',
            'platforms' => [1, 2, 3],
            'serie_id'   => null,
        ],
        [
            'id'    => 2,
            'status'    => GameStatus::ACTIVE,
            'LibGameEn' => 'Mario Kart 8',
            'libGameFr' => 'Mario Kart 8',
            'platforms' => [],
            'serie_id'   => 2,
        ],
        [
            'id'    => 3,
            'status'    => GameStatus::ACTIVE,
            'LibGameEn' => 'Forza Motosport 4',
            'libGameFr' => 'Forza Motosport 4',
            'platforms' => [],
            'serie_id'   => 1,
        ],
        [
            'id'    => 4,
            'status'    => GameStatus::ACTIVE,
            'LibGameEn' => 'Forza Motosport 3',
            'libGameFr' => 'Forza Motosport 3',
            'platforms' => [],
            'serie_id'   => 1,
        ],
        [
            'id'    => 5,
            'status'    => GameStatus::ACTIVE,
            'LibGameEn' => 'Sega Rallye [EN]',
            'libGameFr' => 'Sega Rallye [FR]',
            'platforms' => [],
            'serie_id'   => null,
        ],
        [
            'id'    => 6,
            'status'    => GameStatus::ACTIVE,
            'LibGameEn' => 'Gran Turismo',
            'libGameFr' => 'Gran Turismo',
            'platforms' => [2],
            'serie_id'   => null,
        ],
        [
            'id'    => 7,
            'status'    => GameStatus::ACTIVE,
            'LibGameEn' => 'Jet Set Radio',
            'libGameFr' => 'Jet Set Radio',
            'platforms' => [],
            'serie_id'   => null,
        ],
        [
            'id'    => 11,
            'status'    => GameStatus::ACTIVE,
            'LibGameEn' => 'Mario Kart Double Dash',
            'libGameFr' => 'Mario Kart Double Dash',
            'platforms' => [1],
            'serie_id'   => 2,
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
        $this->loadPlatforms($manager);
        $this->loadGames($manager);
        $manager->flush();
    }


    /**
     * @param ObjectManager $manager
     */
    private function loadPlatforms(ObjectManager $manager): void
    {
        foreach ($this->platforms as $row) {
            $platform = new Platform();
            $platform->setId($row['id']);
            $platform->setName($row['name']);
            $manager->persist($platform);
            $this->addReference('platform.' . $platform->getId(), $platform);
        }
    }


    /**
     * @param ObjectManager $manager
     */
    private function loadGames(ObjectManager $manager): void
    {
        foreach ($this->games as $row) {
            $game = new Game();
            $game->setId($row['id']);
            $game->setStatus($row['status']);
            $game->setLibGameEn($row['LibGameEn']);
            $game->setLibGameFr($row['libGameFr']);
            $game->setCreatedAt(new \Datetime());
            $game->setUpdatedAt(new \Datetime());

            if (null !== $row['serie_id']) {
                $game->setSerie($this->getReference('serie.' . $row['serie_id']));
            }

            foreach ($row['platforms'] as $id) {
                $game->addPlatform($this->getReference('platform.' . $id));
            }

            $manager->persist($game);
            $this->addReference('game' . $game->getId(), $game);
        }
    }
}
