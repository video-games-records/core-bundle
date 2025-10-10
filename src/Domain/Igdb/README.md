# Domain IGDB

Ce répertoire contient toute la logique liée aux mappings et intégrations avec l'API IGDB (Internet Game Database).

## Structure

```
Domain/Igdb/
├── Service/           # Services pour les opérations complexes
├── DataTransformer/   # Transformers Symfony pour les formulaires
├── Mapping/           # Classes de mapping statiques
├── Entity/           # Entités spécifiques IGDB (futures)
├── ValueObject/      # Value objects IGDB (futures)
└── README.md         # Cette documentation
```

## Classes de Mapping

### PlatformMapping (Statique)
Mapping entre les IDs de plateformes VGR et IGDB via constantes.

```php
use VideoGamesRecords\CoreBundle\Domain\Igdb\Mapping\PlatformMapping;

// Obtenir l'ID IGDB à partir de l'ID VGR
$igdbId = PlatformMapping::getIgdbId(1); // 21 (GameCube)

// Obtenir l'ID VGR à partir de l'ID IGDB
$vgrId = PlatformMapping::getVgrId(21); // 1

// Vérifier si un mapping existe
$hasMapping = PlatformMapping::hasMapping(1); // true

// Obtenir des statistiques
$stats = PlatformMapping::getStats();
// ['total' => 75, 'mapped' => 74, 'unmapped' => 1, 'coverage' => 98.67]
```

### GameMapping (Base de données)
Mapping entre les IDs de jeux VGR et IGDB via le champ `igdb_id` en BDD.

```php
use VideoGamesRecords\CoreBundle\Domain\Igdb\Mapping\GameMapping;

public function __construct(
    private readonly GameMapping $gameMapping
) {}

// Obtenir l'ID IGDB à partir de l'ID VGR
$igdbId = $this->gameMapping->getIgdbId(123); // null ou ID IGDB

// Définir un mapping
$success = $this->gameMapping->setMapping(123, 456); // VGR Game 123 => IGDB Game 456

// Supprimer un mapping
$success = $this->gameMapping->removeMapping(123);

// Obtenir des statistiques
$stats = $this->gameMapping->getStats();
// ['total' => 1500, 'mapped' => 450, 'unmapped' => 1050, 'coverage' => 30.0]
```

### GenreMapping (Statique - TODO)
Mapping entre les IDs de genres VGR et IGDB (à implémenter).

## Services

### PlatformMappingService
Service pour les opérations de mapping des plateformes avec injection de dépendances.

```php
use VideoGamesRecords\CoreBundle\Domain\Igdb\Service\PlatformMappingService;

public function __construct(
    private readonly PlatformMappingService $platformMappingService
) {}

public function example(): void
{
    $igdbId = $this->platformMappingService->getIgdbPlatformId(1);
    $vgrIds = $this->platformMappingService->getVgrPlatformsWithIgdbMapping();
}
```

### IgdbMappingService
Service central pour tous les types de mappings IGDB.

```php
use VideoGamesRecords\CoreBundle\Domain\Igdb\Service\IgdbMappingService;

public function __construct(
    private readonly IgdbMappingService $igdbMappingService
) {}

public function example(): void
{
    // Platform mappings
    $platformIgdbId = $this->igdbMappingService->getPlatformIgdbId(1);
    
    // Game mappings (base de données)
    $gameIgdbId = $this->igdbMappingService->getGameIgdbId(123);
    $this->igdbMappingService->setGameMapping(123, 456);
    
    // Statistiques globales
    $stats = $this->igdbMappingService->getAllMappingStats();
}
```

## DataTransformers

### PlatformMappingTransformer
Transformer Symfony pour les formulaires qui convertit automatiquement entre les IDs VGR et IGDB.

```php
use VideoGamesRecords\CoreBundle\Domain\Igdb\DataTransformer\PlatformMappingTransformer;

$builder->add('platform_id', IntegerType::class)
    ->addModelTransformer($this->platformMappingTransformer);
```

## Ajouter un nouveau type de mapping

1. **Créer la classe de mapping** dans `Mapping/`:

```php
<?php
namespace VideoGamesRecords\CoreBundle\Domain\Igdb\Mapping;

class NewEntityMapping extends AbstractMapping
{
    private const VGR_TO_IGDB_MAPPING = [
        // vos mappings ici
    ];

    public static function getIgdbId(int $vgrId): ?int
    {
        return self::VGR_TO_IGDB_MAPPING[$vgrId] ?? null;
    }

    public static function getMapping(): array
    {
        return self::VGR_TO_IGDB_MAPPING;
    }
}
```

2. **Ajouter les méthodes** dans `IgdbMappingService`:

```php
public function getNewEntityIgdbId(int $vgrId): ?int
{
    return NewEntityMapping::getIgdbId($vgrId);
}
```

3. **Créer un service spécialisé** si nécessaire dans `Service/`.

4. **Créer un transformer** si nécessaire dans `DataTransformer/`.

## Approches de Mapping

### 🔹 Mapping Statique (Plateformes, Genres)
Utilisé pour des entités avec un nombre limité et stable d'éléments.

**Avantages** :
- Performance maximale (pas d'accès BDD)
- Simple à maintenir
- Versionnable avec le code

**Inconvénients** :
- Nécessite un redéploiement pour changer les mappings
- Limité aux petits volumes

**Cas d'usage** : Plateformes (75 éléments), Genres

### 🔹 Mapping Base de Données (Jeux)
Utilisé pour des entités avec un grand nombre d'éléments variables.

**Avantages** :
- Évolutif pour de gros volumes
- Modifiable sans redéploiement
- Gestion fine des mappings

**Inconvénients** :
- Requêtes BDD nécessaires
- Plus complexe à maintenir

**Cas d'usage** : Jeux (milliers d'éléments), Companies

## Conventions

- **Mappings statiques** : Héritez de `AbstractMapping` et implémentez `MappingInterface`
- **Mappings BDD** : Injectez le repository approprié et implémentez les mêmes méthodes
- **Logique métier** : Utilisez les services dans `Service/` pour des opérations plus complexes
- **Formulaires** : Utilisez les transformers dans `DataTransformer/` pour l'intégration Symfony

## Migration depuis l'ancienne structure

L'ancien `Tools/PlatformMapping` est maintenant déprécié et délègue vers la nouvelle structure.
Migrez votre code vers `Domain\Igdb\Mapping\PlatformMapping`.

Avant :
```php
use VideoGamesRecords\CoreBundle\Tools\PlatformMapping;
$igdbId = PlatformMapping::vgrToIgdb(1);
```

Après :
```php
use VideoGamesRecords\CoreBundle\Domain\Igdb\Mapping\PlatformMapping;
$igdbId = PlatformMapping::getIgdbId(1);
```