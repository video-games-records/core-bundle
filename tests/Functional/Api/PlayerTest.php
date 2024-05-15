<?php

namespace VideoGamesRecords\CoreBundle\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use VideoGamesRecords\CoreBundle\Entity\Player;

class PlayerTest extends ApiTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
    }

    public function testGetCollection(): void
    {
        self::bootKernel();
        $response = static::createClient()->request('GET', '/api/players');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(
            Player::class,
            null,
            'jsonld',
            ['groups' => [
                'player:read',
                'player:team', 'team:read',
                'player:country', 'country:read',
                'player:status', 'player-status:read']]
        );
    }

    public function testGet(): void
    {
        $response = static::createClient()->request('GET', '/api/players/1');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(
            Player::class,
            null,
            'jsonld',
            ['groups' => [
                'player:read',
                'player:team', 'team:read',
                'player:country', 'country:read',
                'player:status', 'player-status:read']]
        );
    }
}
