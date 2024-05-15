<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait IsRankTrait
{
    #[ORM\Column(nullable: false, options: ['default' => true])]
    private bool $isRank = true;

    public function setIsRank(bool $isRank): void
    {
        $this->isRank = $isRank;
    }

    public function getIsRank(): bool
    {
        return $this->isRank;
    }
}
