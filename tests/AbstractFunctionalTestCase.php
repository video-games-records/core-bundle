<?php

namespace VideoGamesRecords\CoreBundle\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractFunctionalTestCase extends WebTestCase
{
    private static bool $schemaBuilt = false;

    protected static function setUpSchema(bool $forceCreation = false): void
    {
        if (!self::$booted) {
            self::bootKernel();
        }

        if (!self::$schemaBuilt || $forceCreation) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = self::getContainer()->get('doctrine.orm.default_entity_manager');

            self::dropSchema($entityManager);
            self::createSchema($entityManager);
        }
    }

    private static function createSchema(EntityManagerInterface $entityManager): void
    {
        $metadatas  = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metadatas);

        self::$schemaBuilt = true;
    }

    private static function dropSchema(EntityManagerInterface $entityManager): void
    {
        $metadatas  = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropSchema($metadatas);
    }
}
