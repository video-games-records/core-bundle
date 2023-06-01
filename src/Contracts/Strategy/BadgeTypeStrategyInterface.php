<?php

namespace VideoGamesRecords\CoreBundle\Contracts\Strategy;


use VideoGamesRecords\CoreBundle\Contracts\BadgeInterface;
use VideoGamesRecords\CoreBundle\Entity\Badge;

interface BadgeTypeStrategyInterface extends BadgeInterface
{
    public function supports(Badge $badge): bool;

    public function getTitle(Badge $badge): string;
}
