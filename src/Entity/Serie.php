<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Controller\Serie\Player\GetRankingPoints;
use VideoGamesRecords\CoreBundle\Controller\Serie\Player\GetRankingMedals;
use VideoGamesRecords\CoreBundle\Repository\SerieRepository;
use VideoGamesRecords\CoreBundle\Traits\Accessor\CurrentLocale;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbGameTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PictureTrait;
use VideoGamesRecords\CoreBundle\ValueObject\SerieStatus;

#[ORM\Table(name:'vgr_serie')]
#[ORM\Entity(repositoryClass: SerieRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\SerieListener"])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Get(
            uriTemplate: '/series/{id}/player-ranking-points',
            controller: GetRankingPoints::class,
            normalizationContext: ['groups' => [
                'player-serie:read',
                'player-serie:player', 'player:read:minimal',
                'player:team', 'team:read:minimal',
                'player:country', 'country:read']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves the player points leaderboard',
                description: 'Retrieves the player points leaderboard'
            ),
        ),
        new Get(
            uriTemplate: '/series/{id}/player-ranking-medals',
            controller: GetRankingMedals::class,
            normalizationContext: ['groups' => [
                'player-serie:read',
                'player-serie:player', 'player:read:minimal',
                'player:team', 'team:read:minimal',
                'player:country', 'country:read']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves the player medals leaderboard',
                description: 'Retrieves the player medals leaderboard'
            ),
        ),
    ],
    normalizationContext: ['groups' => ['serie:read']]
)]
#[ApiFilter(SearchFilter::class, properties: ['status' => 'exact', 'libSerie' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['libSerie'])]
class Serie
{
    use TimestampableEntity;
    use NbChartTrait;
    use NbGameTrait;
    use PictureTrait;
    use CurrentLocale;

    private const string DEFAULT_LOCALE = 'en';

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(name: 'libSerie', length: 255, nullable: false)]
    private string $libSerie;

    #[ORM\Column(nullable: false)]
    private string $status = SerieStatus::INACTIVE;


    #[ORM\Column(length: 128)]
    #[Gedmo\Slug(fields: ['libSerie'])]
    protected string $slug;

    /**
     * @var Collection<int, Game>
     */
    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'serie')]
    private Collection $games;


    #[ORM\OneToOne(targetEntity: Badge::class, inversedBy: 'serie', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name:'badge_id', referencedColumnName:'id', nullable:true, onDelete: 'SET NULL')]
    private ?Badge $badge;

    /** @var Collection<SerieTranslation> */
    #[ORM\OneToMany(
        targetEntity: SerieTranslation::class,
        mappedBy: 'translatable',
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
        indexBy: 'locale'
    )]
    private Collection $translations;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s [%s]', $this->getDefaultName(), $this->id);
    }

    public function getDefaultName(): string
    {
        return $this->libSerie;
    }

    public function getName(): string
    {
        return $this->libSerie;
    }

    public function setLibSerie(string $libSerie): void
    {
        $this->libSerie = $libSerie;
    }

    public function getLibSerie(): string
    {
        return $this->libSerie;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getSerieStatus(): SerieStatus
    {
        return new SerieStatus($this->status);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getGames(): Collection
    {
        return $this->games;
    }

    public function setBadge($badge = null): void
    {
        $this->badge = $badge;
    }

    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function setTranslations(Collection $translations): void
    {
        $this->translations = $translations;
    }

    public function addTranslation(SerieTranslation $translation): void
    {
        if (!$this->translations->contains($translation)) {
            $translation->setTranslatable($this);
            $this->translations->set($translation->getLocale(), $translation);
        }
    }

    public function removeTranslation(SerieTranslation $translation): void
    {
        $this->translations->removeElement($translation);
    }

    public function translate(?string $locale = null, bool $fallbackToDefault = true): ?SerieTranslation
    {
        $locale = $locale ?: $this->currentLocale ?: self::DEFAULT_LOCALE;

        // If translation exists for requested locale
        if ($this->translations->containsKey($locale)) {
            return $this->translations->get($locale);
        }

        // Fallback to default locale if enabled and different from requested locale
        if (
            $fallbackToDefault
            && $locale !== self::DEFAULT_LOCALE
            && $this->translations->containsKey(self::DEFAULT_LOCALE)
        ) {
            return $this->translations->get(self::DEFAULT_LOCALE);
        }

        // Last resort: return first translation even if empty
        return $this->translations->first() ?: null;
    }

    public function getAvailableLocales(): array
    {
        return $this->translations->getKeys();
    }

    public function setDescription(string $description, ?string $locale = null): void
    {
        $locale = $locale ?: $this->currentLocale ?: self::DEFAULT_LOCALE;

        if (!$this->translations->containsKey($locale)) {
            $translation = new SerieTranslation();
            $translation->setTranslatable($this);
            $translation->setLocale($locale);
            $this->translations->set($locale, $translation);
        }

        $this->translations->get($locale)->setDescription($description);
    }

    public function getDescription(?string $locale = null): ?string
    {
        $translation = $this->translate($locale);
        return $translation?->getDescription();
    }
}
