<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\RuleRepository;
use VideoGamesRecords\CoreBundle\Traits\Accessor\CurrentLocale;

#[ORM\Table(name:'vgr_rule')]
#[ORM\Entity(repositoryClass: RuleRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\RuleListener"])]
class Rule
{
    use TimestampableEntity;
    use CurrentLocale;

    private const string DEFAULT_LOCALE = 'en';

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\Length(min:3, max: 100)]
    #[ORM\Column(length: 100, nullable: false, unique: true)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name:'player_id', referencedColumnName:'id', nullable:true)]
    private ?Player $player;

    /**
     * @var Collection<int, Game>
     */
    #[Orm\ManyToMany(targetEntity: Game::class, mappedBy: 'rules')]
    private Collection $games;

    /** @var Collection<RuleTranslation> */
    #[ORM\OneToMany(
        targetEntity: RuleTranslation::class,
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

    public function __toString()
    {
        return sprintf('%s [%s]', $this->getName(), $this->id);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function setTranslations(Collection $translations): void
    {
        $this->translations = $translations;
    }

    public function addTranslation(RuleTranslation $translation): void
    {
        if (!$this->translations->contains($translation)) {
            $translation->setTranslatable($this);
            $this->translations->set($translation->getLocale(), $translation);
        }
    }

    public function removeTranslation(RuleTranslation $translation): void
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Retrieves a translation with intelligent fallback logic.
     * Ensures content quality by checking for non-empty translations.
     */
    public function translate(?string $locale = null, bool $fallbackToDefault = true): ?RuleTranslation
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

    public function setContent(string $content, ?string $locale = null): void
    {
        $locale = $locale ?: $this->currentLocale ?: self::DEFAULT_LOCALE;

        if (!$this->translations->containsKey($locale)) {
            $translation = new RuleTranslation();
            $translation->setTranslatable($this);
            $translation->setLocale($locale);
            $this->translations->set($locale, $translation);
        }

        $this->translations->get($locale)->setContent($content);
    }

    public function getContent(?string $locale = null): ?string
    {
        $translation = $this->translate($locale);
        return $translation?->getContent();
    }

    /**
     * @return Collection
     */
    public function getGames(): Collection
    {
        return $this->games;
    }
}
