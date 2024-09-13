<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Scheduler\Message;

class UpdateYoutubeData
{
    private ?int $nb = null;

    public function getNb(): ?int
    {
        return $this->nb;
    }
}
