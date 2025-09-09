<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;
use VideoGamesRecords\CoreBundle\ValueObject\GameStatus;

class GameSearchFilter extends AbstractFilter
{
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if ($property !== 'search') {
            return;
        }

        if (empty($value)) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $parameterName = $queryNameGenerator->generateParameterName('search');

        $queryBuilder
            ->andWhere($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq("$alias.status", ':status'),
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like("LOWER($alias.libGameEn)", "LOWER(:$parameterName)"),
                    $queryBuilder->expr()->like("LOWER($alias.libGameFr)", "LOWER(:$parameterName)"),
                )
            ))
            ->setParameter($parameterName, '%' . $value . '%')
            ->setParameter('status', GameStatus::ACTIVE);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'search' => [
                'property' => 'search',
                'type' => 'string',
                'required' => false,
                'description' => 'Recherche dans les noms de jeux (français et anglais)',
                'openapi' => [
                    'example' => 'mario',
                    'allowReserved' => false,
                    'allowEmptyValue' => true,
                    'explode' => false,
                ],
            ],
        ];
    }
}
