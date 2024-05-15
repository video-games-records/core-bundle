<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\PictureRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\Game\GameTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\Player\PlayerTrait;

#[ORM\Table(name:'vgr_picture')]
#[ORM\Entity(repositoryClass: PictureRepository::class)]
class Picture
{
    use PlayerTrait;
    use GameTrait;
    use TimestampableEntity;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: false)]
    private string $path = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $metadata = null;

    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: false)]
    private string $hash;


    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setMetadata(string $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getMetadata(): ?string
    {
        return $this->metadata;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function __toString()
    {
        return sprintf('Picture [%s]', $this->id);
    }
}
