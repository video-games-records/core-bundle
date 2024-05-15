<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait IsDlcTrait
{
    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $isDlc = false;

    public function setIsDlc(bool $isDlc): void
    {
        $this->isDlc = $isDlc;
    }

    public function getIsDlc(): bool
    {
        return $this->isDlc;
    }
}
