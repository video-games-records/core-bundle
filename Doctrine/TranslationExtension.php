<?php
namespace VideoGamesRecords\CoreBundle\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Locale;
use VideoGamesRecords\CoreBundle\Entity\Serie;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\PlayerGame;
use VideoGamesRecords\CoreBundle\Entity\TeamGame;

final class TranslationExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /**
     * @param QueryBuilder                $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string                      $resourceClass
     * @param string|null                 $operationName
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ) {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    /**
     * @param QueryBuilder                $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string                      $resourceClass
     * @param array                       $identifiers
     * @param string|null                 $operationName
     * @param array                       $context
     */
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ) {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string       $resourceClass
     */
    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (!in_array($resourceClass, array(Country::class, Serie::class, Game::class, Group::class, Chart::class, PlayerGame::class, TeamGame::class))) {
            return;
        }
        $locale = Locale::getDefault();
        if (!in_array($locale, array('en', 'fr'))) {
            $locale = 'en';
        }
        if (in_array($resourceClass, array(PlayerGame::class, TeamGame::class))) {
            $queryBuilder->join('o.game', 'g')
                ->addSelect('g')
                ->leftJoin('g.translations', 't', 'WITH', "t.locale='$locale'")
                ->addSelect('t');
        } else {
            $queryBuilder->leftJoin('o.translations', 't', 'WITH', "t.locale='$locale'")
                ->addSelect('t');
        }
    }
}
