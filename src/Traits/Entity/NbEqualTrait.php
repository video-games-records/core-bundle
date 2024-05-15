<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait NbEqualTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 1])]
    private int $nbEqual = 1;

    public function setNbEqual(int $nbEqual): void
    {
        $this->nbEqual = $nbEqual;
    }

    public function getNbEqual(): int
    {
        return $this->nbEqual;
    }
}
