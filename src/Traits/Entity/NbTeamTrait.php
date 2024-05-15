<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait NbTeamTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbTeam = 0;

    public function setNbTeam(int $nbTeam): void
    {
        $this->nbTeam = $nbTeam;
    }

    public function getNbTeam(): int
    {
        return $this->nbTeam;
    }
}
