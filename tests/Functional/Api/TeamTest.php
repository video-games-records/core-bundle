<?php

namespace VideoGamesRecords\CoreBundle\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use VideoGamesRecords\CoreBundle\Entity\Team;

class TeamTest extends ApiTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
    }

    public function testGetCollection(): void
    {
        self::bootKernel();
        $response = static::createClient()->request('GET', '/api/teams');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(
            Team::class,
            null,
            'jsonld',
            ['groups' => ['team:read']]
        );
    }

    public function testGet(): void
    {
        $response = static::createClient()->request('GET', '/api/teams/1');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(
            Team::class,
            null,
            'jsonld',
            ['groups' => ['team:read', 'team:leader', 'player:leader']]
        );
    }
}
