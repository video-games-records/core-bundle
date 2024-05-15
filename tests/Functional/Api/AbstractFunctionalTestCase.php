<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\Client;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class AbstractFunctionalTestCase extends ApiTestCase
{
    protected Client $apiClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiClient = static::createClient();
    }
}
