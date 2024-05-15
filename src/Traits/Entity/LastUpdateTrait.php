<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait LastUpdateTrait
{
    #[ORM\Column(nullable: true)]
    private ?DateTime $lastUpdate;

    public function setLastUpdate(?DateTime $lastUpdate): void
    {
        $this->lastUpdate = $lastUpdate;
    }

    public function getLastUpdate(): ?DateTime
    {
        return $this->lastUpdate;
    }
}
