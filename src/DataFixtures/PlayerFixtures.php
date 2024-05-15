<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ObjectManager;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\Player;

class PlayerFixtures extends Fixture
{
    /**
     * @var array<string>
     */
    private array $entities = [
        'Player',
    ];


    /**
     * @var array<mixed>
     */
    private array $players = [
        [
            'id' => 11,
            'pseudo'   => 'magicbart',
        ],
        [
            'id' => 12,
            'pseudo'   => 'kloh',
        ],
        [
            'id' => 13,
            'pseudo'   => 'flatine',
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
        $this->loadPlayers($manager);
        $manager->flush();
    }


    /**
     * @param ObjectManager $manager
     */
    private function loadPlayers(ObjectManager $manager): void
    {
        foreach ($this->players as $row) {
            $player = new Player();
            $player->setId($row['id']);
            $player->setUserId($row['id']);
            $player->setPseudo($row['pseudo']);
            $player->setStatus($this->getReference('playerStatus1'));
            $player->setCreatedAt(new \Datetime());
            $player->setUpdatedAt(new \Datetime());

            $manager->persist($player);
            $this->addReference('player' . $player->getId(), $player);
        }
    }
}
