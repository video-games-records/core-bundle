<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Controller\Country\GetRanking;
use VideoGamesRecords\CoreBundle\Repository\CountryRepository;
use VideoGamesRecords\CoreBundle\Traits\Accessor\CurrentLocale;

#[ORM\Table(name:'vgr_country')]
#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ApiResource(
    paginationEnabled: false,
    order: ['translations.name' => 'ASC'],
    operations: [
        new GetCollection(),
        new Get(),
        new Get(
            uriTemplate: '/countries/{id}/player-ranking',
            controller: GetRanking::class,
            normalizationContext: ['groups' => [
                'player:read', 'player:team', 'team:read:minimal']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves the country leaderboard',
                description: 'Retrieves the country leaderboard'
            ),
            /*openapiContext: [
            'parameters' => [
            [
            'name' => 'maxRank',
            'in' => 'query',
            'type' => 'integer',
            'required' => false
            ],
            ]
            ]*/
        ),
    ],
    normalizationContext: ['groups' => ['country:read']]
)]
class Country
{
    use CurrentLocale;

    private const string DEFAULT_LOCALE = 'en';

    #[Assert\Length(max: 2)]
    #[ORM\Column(length: 2, nullable: false)]
    private string $codeIso2;

    #[Assert\Length(max: 3)]
    #[ORM\Column(length: 3, nullable: false)]
    private string $codeIso3;

    #[ORM\Column(nullable: false)]
    private int $codeIsoNumeric;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 255, nullable: true)]
    private string $slug;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Badge::class, cascade: ['persist'], inversedBy: 'country')]
    #[ORM\JoinColumn(name:'badge_id', referencedColumnName:'id', nullable:true)]
    private ?Badge $badge;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $boolMaj = false;

    /** @var Collection<CountryTranslation> */
    #[ORM\OneToMany(
        targetEntity: CountryTranslation::class,
        mappedBy: 'translatable',
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
        indexBy: 'locale'
    )]
    private Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }


    public function setCodeIso2(string $codeIso2): void
    {
        $this->codeIso2 = $codeIso2;
    }

    public function getCodeIso2(): string
    {
        return $this->codeIso2;
    }

    public function getCodeIso3(): string
    {
        return $this->codeIso3;
    }

    public function setCodeIso3(string $codeIso3): void
    {
        $this->codeIso3 = $codeIso3;
    }

    public function getCodeIsoNumeric(): int
    {
        return $this->codeIsoNumeric;
    }

    public function setCodeIsoNumeric(int $codeIsoNumeric): void
    {
        $this->codeIsoNumeric = $codeIsoNumeric;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }


    public function setBadge(?Badge $badge = null): void
    {
        $this->badge = $badge;
    }

    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    public function setBoolMaj(bool $boolMaj): void
    {
        $this->boolMaj = $boolMaj;
    }

    public function getBoolMaj(): bool
    {
        return $this->boolMaj;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function __toString()
    {
        return sprintf('%s [%d]', $this->getDefaultName(), $this->getId());
    }

    public function getDefaultName(): string
    {
        return $this->translate('en', false)->getName();
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function setTranslations(Collection $translations): void
    {
        $this->translations = $translations;
    }

    public function addTranslation(CountryTranslation $translation): void
    {
        if (!$this->translations->contains($translation)) {
            $translation->setTranslatable($this);
            $this->translations->set($translation->getLocale(), $translation);
        }
    }

    public function removeTranslation(CountryTranslation $translation): void
    {
        $this->translations->removeElement($translation);
    }

    public function translate(?string $locale = null, bool $fallbackToDefault = true): ?CountryTranslation
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

    public function setName(string $name, ?string $locale = null): void
    {
        $locale = $locale ?: $this->currentLocale ?: self::DEFAULT_LOCALE;

        if (!$this->translations->containsKey($locale)) {
            $translation = new CountryTranslation();
            $translation->setTranslatable($this);
            $translation->setLocale($locale);
            $this->translations->set($locale, $translation);
        }

        $this->translations->get($locale)->setDescription($name);
    }

    public function getName(?string $locale = null): ?string
    {
        $translation = $this->translate($locale);
        return $translation?->getName();
    }
}
