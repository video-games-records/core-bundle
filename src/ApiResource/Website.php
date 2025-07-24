<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use VideoGamesRecords\CoreBundle\Controller\Website\GetStats;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/website/get-stats',
            controller: GetStats::class,
            openapi: new Model\Operation(
                summary: 'Return website stats',
                description: 'Return website stats',
            ),
            read: false
        )
    ],
)]

class Website
{
}
