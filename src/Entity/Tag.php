<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\TagRepository;

#[ORM\Table(name:'vgr_tag')]
#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Index(name: "idx_category", columns: ["category"])]
#[ORM\Index(name: "idx_is_official", columns: ["is_official"])]
#[ApiFilter(OrderFilter::class, properties: ['name', 'category'], arguments: ['orderParameterName' => 'order'])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
    ],
    normalizationContext: ['groups' => ['tag:read']]
)]
class Tag
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: false)]
    private string $name;

    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $isOfficial = false;

    public function __toString(): string
    {
        return sprintf('%s [%s]', $this->getName(), $this->getId());
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setIsOfficial(bool $isOfficial): void
    {
        $this->isOfficial = $isOfficial;
    }

    public function isOfficial(): bool
    {
        return $this->isOfficial;
    }

    public function getIsOfficial(): bool
    {
        return $this->isOfficial;
    }
}
