<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\DescriptionTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="vgr_serie_translation")
 */
class SerieTranslation implements TranslationInterface
{
    use TranslationTrait;
    use DescriptionTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
