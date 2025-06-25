<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name:'vgr_rule_translation')]
#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'rule_translation_unique', columns: ['translatable_id', 'locale'])]
class RuleTranslation
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Rule::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'translatable_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Rule $translatable;

    #[ORM\Column(length: 5)]
    private string $locale;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $content = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTranslatable(): Rule
    {
        return $this->translatable;
    }

    public function setTranslatable(Rule $translatable): void
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

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
