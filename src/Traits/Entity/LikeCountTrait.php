<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait LikeCountTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $likeCount = 0;

    public function setLikeCount(int $likeCount): void
    {
        $this->likeCount = $likeCount;
    }

    public function getLikeCount(): int
    {
        return $this->likeCount;
    }
}
