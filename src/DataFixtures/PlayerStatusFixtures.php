<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ObjectManager;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\PlayerStatus;

class PlayerStatusFixtures extends Fixture
{
    /**
     * @var array<string>
     */
    private array $entities = [
        'PlayerStatus',
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
        $manager->flush();
    }


    /**
     * @param $manager
     */
    public function loadStatus($manager): void
    {
        $playerStatus = new PlayerStatus();
        $playerStatus->setId(1);
        $playerStatus->setClass('normal');
        $manager->persist($playerStatus);
        $this->addReference('playerStatus1', $playerStatus);
    }
}
