<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name:'vgr_player_status_translation')]
#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'player_status_translation_unique', columns: ['translatable_id', 'locale'])]
class PlayerStatusTranslation
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: PlayerStatus::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private PlayerStatus $translatable;

    #[ORM\Column(length: 5)]
    private string $locale;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: false)]
    private string $name = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTranslatable(): PlayerStatus
    {
        return $this->translatable;
    }

    public function setTranslatable(PlayerStatus $translatable): void
    {
        $this->translatable = $translatable;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
