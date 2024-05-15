<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait NbVideoTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbVideo = 0;

    public function setNbVideo(int $nbVideo): void
    {
        $this->nbVideo = $nbVideo;
    }

    public function getNbVideo(): int
    {
        return $this->nbVideo;
    }
}
