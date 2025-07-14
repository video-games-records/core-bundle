<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

class TeamSearchFilter extends AbstractFilter
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
            ->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like("LOWER({$alias}.libTeam)", "LOWER(:{$parameterName})"),
                $queryBuilder->expr()->like("LOWER({$alias}.tag)", "LOWER(:{$parameterName})")
            ))
            ->setParameter($parameterName, '%' . $value . '%');
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'search' => [
                'property' => 'search',
                'type' => 'string',
                'required' => false,
                'description' => 'Recherche dans le nom de l\'équipe (libTeam) ou dans le tag',
                'openapi' => [
                    'example' => 'TeamName',
                    'allowReserved' => false,
                    'allowEmptyValue' => true,
                    'explode' => false,
                ],
            ],
        ];
    }
}
