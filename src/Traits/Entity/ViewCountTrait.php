<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ViewCountTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $viewCount = 0;

    public function setViewCount(int $viewCount): void
    {
        $this->viewCount = $viewCount;
    }

    public function getViewCount(): int
    {
        return $this->viewCount;
    }
}
