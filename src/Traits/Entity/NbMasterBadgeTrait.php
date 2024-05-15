<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait NbMasterBadgeTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbMasterBadge = 0;

    public function getNbMasterBadge(): int
    {
        return $this->nbMasterBadge;
    }

    public function setNbMasterBadge(int $nbMasterBadge): void
    {
        $this->nbMasterBadge = $nbMasterBadge;
    }
}
