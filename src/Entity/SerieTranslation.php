<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Traits\Entity\DescriptionTrait;

#[ORM\Table(name:'vgr_serie_translation')]
#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'serie_translation_unique', columns: ['translatable_id', 'locale'])]
class SerieTranslation
{
    use DescriptionTrait;

    #[ORM\ManyToOne(targetEntity: Serie::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Serie $translatable;

    #[ORM\Column(length: 5)]
    private string $locale;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTranslatable(): Serie
    {
        return $this->translatable;
    }

    public function setTranslatable(Serie $translatable): void
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
}
