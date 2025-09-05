<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Player\Friend;

use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Player;

class GetFriends extends AbstractController
{
    public function __invoke(Player $player): Collection
    {
        return $player->getFriends();
    }
}
