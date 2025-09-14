<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\PlayerBadge;

class PlayerBadgeObtained extends Event
{
    protected PlayerBadge $playerBadge;

    public function __construct(PlayerBadge $playerBadge)
    {
        $this->playerBadge = $playerBadge;
    }

    public function getPlayerBadge(): PlayerBadge
    {
        return $this->playerBadge;
    }
}