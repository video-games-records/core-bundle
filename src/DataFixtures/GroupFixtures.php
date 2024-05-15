<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ObjectManager;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\Group;

class GroupFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            GameFixtures::class
        ];
    }

    /**
     * @var array<string>
     */
    private array $entities = [
        'Group',
    ];



    /**
     * @var array<mixed>
     */
    private array $groups = [
        [
            'id' => 1,
            'game_id' => 11,
            'LibGroupEn' => 'Meilleur Tour',
            'libGroupFr' => 'Fastest Lap Times',
        ],
        [
            'id'   => 2,
            'game_id' => 11,
            'LibGroupEn' => 'Meilleur Temps',
            'libGroupFr' => 'Fastest Total Times',
        ],
        [
            'id'   => 3,
            'game_id' => 11,
            'LibGroupEn' => 'Grand Prix',
            'libGroupFr' => 'Grand Prix',
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
        $this->loadGroups($manager);
        $manager->flush();
    }


    /**
     * @param ObjectManager $manager
     */
    private function loadGroups(ObjectManager $manager): void
    {
        foreach ($this->groups as $row) {
            $group = new Group();
            $group->setId($row['id']);
            $group->setLibGroupEn($row['LibGroupEn']);
            $group->setLibGroupFr($row['libGroupFr']);
            $group->setCreatedAt(new \Datetime());
            $group->setUpdatedAt(new \Datetime());
            $group->setGame($this->getReference('game' . $row['game_id']));
            $manager->persist($group);
            $this->addReference('group' . $group->getId(), $group);
        }
    }
}
