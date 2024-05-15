<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait NbPostTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbPost = 0;

    public function setNbPost(int $nbPost): void
    {
        $this->nbPost = $nbPost;
    }

    public function getNbPost(): int
    {
        return $this->nbPost;
    }
}
