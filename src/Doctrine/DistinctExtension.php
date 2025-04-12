<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use VideoGamesRecords\CoreBundle\Entity\Game;

final class DistinctExtension implements QueryCollectionExtensionInterface
{
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $this->distinct($queryBuilder, $resourceClass);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string       $resourceClass
     */
    private function distinct(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if ($resourceClass != Game::class) {
            return;
        }

        $queryBuilder->distinct();
    }
}
