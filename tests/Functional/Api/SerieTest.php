<?php

namespace VideoGamesRecords\CoreBundle\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use VideoGamesRecords\CoreBundle\Entity\Serie;

class SerieTest extends ApiTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
    }

    public function testGetCollection(): void
    {
        self::bootKernel();
        $response = static::createClient()->request('GET', '/api/series');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(
            Serie::class,
            null,
            'jsonld',
            ['groups' => ['serie:read']]
        );
    }

    public function testGet(): void
    {
        $response = static::createClient()->request('GET', '/api/series/1');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(
            Serie::class,
            null,
            'jsonld',
            ['groups' => ['serie:read']]
        );
    }
}
