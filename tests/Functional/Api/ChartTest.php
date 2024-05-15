<?php

namespace VideoGamesRecords\CoreBundle\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use VideoGamesRecords\CoreBundle\Entity\Chart;

class ChartTest extends ApiTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
    }

    public function testGetCollection(): void
    {
        self::bootKernel();
        $response = static::createClient()->request('GET', '/api/charts');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(
            Chart::class,
            null,
            'jsonld',
            ['groups' => ['chart:read']]
        );
    }

    public function testGet(): void
    {
        $response = static::createClient()->request('GET', '/api/charts/1');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(
            Chart::class,
            null,
            'jsonld',
            ['groups' => ['chart:read', 'chart:libs', 'chart-lib:read']]
        );
    }
}
