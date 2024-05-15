<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ObjectManager;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\Serie;

class SerieFixtures extends Fixture
{
    /**
     * @var array<string>
     */
    private array $entities = [
        'Serie',
    ];

    /**
     * @var array<mixed>
     */
    private array $series = [
        [
            'id'        => 1,
            'libSerie' => 'Forza Motosport',
        ],
        [
            'id'        => 2,
            'libSerie' => 'Mario Kart',
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
        $this->loadSeries($manager);
        $manager->flush();
    }


    /**
     * @param ObjectManager $manager
     */
    private function loadSeries(ObjectManager $manager): void
    {
        foreach ($this->series as $row) {
            $serie = new Serie();
            $serie->setId($row['id']);
            $serie->setLibSerie($row['libSerie']);
            $serie->setCreatedAt(new \Datetime());
            $serie->setUpdatedAt(new \Datetime());
            $manager->persist($serie);
            $this->addReference('serie.' . $serie->getId(), $serie);
        }
    }
}
