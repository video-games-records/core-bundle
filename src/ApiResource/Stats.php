<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use VideoGamesRecords\CoreBundle\Controller\GetWebsiteStats;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/stats/website',
            controller: GetWebsiteStats::class,
            read: false,
            openapi: new Model\Operation(
                summary: 'Return website stats',
                description: 'Return website stats',
            )
        )
    ],
)]

class Stats
{
}
