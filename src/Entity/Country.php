<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableMethodsTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatablePropertiesTrait;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\CountryRepository;

#[ORM\Table(name:'vgr_country')]
#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ApiResource(order: ['translations.name' => 'ASC'])]
class Country implements TranslatableInterface
{
    use TranslatablePropertiesTrait;
    use TranslatableMethodsTrait;

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

    /**
     * Set codeIso
     *
     * @param string $codeIso2
     * @return Country
     */
    public function setCodeIso2(string $codeIso2): Country
    {
        $this->codeIso2 = $codeIso2;

        return $this;
    }

    /**
     * Get codeIso
     *
     * @return string
     */
    public function getCodeIso2(): string
    {
        return $this->codeIso2;
    }

    /**
     * @return string
     */
    public function getCodeIso3(): string
    {
        return $this->codeIso3;
    }

    /**
     * @param string $codeIso3
     * @return Country
     */
    public function setCodeIso3(string $codeIso3): Country
    {
        $this->codeIso3 = $codeIso3;
        return $this;
    }

    /**
     * @return int
     */
    public function getCodeIsoNumeric(): int
    {
        return $this->codeIsoNumeric;
    }

    /**
     * @param int $codeIsoNumeric
     * @return Country
     */
    public function setCodeIsoNumeric(int $codeIsoNumeric): Country
    {
        $this->codeIsoNumeric = $codeIsoNumeric;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): Country
    {
        $this->translate(null, false)->setName($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->translate(null, false)->getName();
    }

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId(int $id): Country
    {
        $this->id = $id;

        return $this;
    }

    /**
     * set badge
     * @param Badge|null $badge
     * @return $this
     */
    public function setBadge(Badge $badge = null): Country
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Get idBadge
     * @return Badge|null
     */
    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    /**
     * Set boolMaj
     *
     * @param boolean $boolMaj
     * @return $this
     */
    public function setBoolMaj(bool $boolMaj): Country
    {
        $this->boolMaj = $boolMaj;

        return $this;
    }

    /**
     * Get boolMaj
     *
     * @return bool
     */
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

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%d]', $this->getDefaultName(), $this->getId());
    }

    /**
     * @return string
     */
    public function getDefaultName(): string
    {
        return $this->translate('en', false)->getName();
    }
}
