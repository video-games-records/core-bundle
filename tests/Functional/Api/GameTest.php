<?php

namespace VideoGamesRecords\CoreBundle\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use VideoGamesRecords\CoreBundle\Entity\Game;

class GameTest extends ApiTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
    }

    public function testGetCollection(): void
    {
        self::bootKernel();
        $response = static::createClient()->request('GET', '/api/games');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(
            Game::class,
            null,
            'jsonld',
            ['groups' => ['game:read']]
        );
    }

    public function testGet(): void
    {
        $response = static::createClient()->request('GET', '/api/games/1');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(
            Game::class,
            null,
            'jsonld',
            ['groups' => ['game:read',
                'game:platforms', 'platform:read',
                'game:serie', 'serie:read',
                'game:rules', 'rule:read']]
        );
    }
}
