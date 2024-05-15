<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\DescriptionTrait;

#[ORM\Table(name:'vgr_serie_translation')]
#[ORM\Entity]
class SerieTranslation implements TranslationInterface
{
    use TranslationTrait;
    use DescriptionTrait;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
