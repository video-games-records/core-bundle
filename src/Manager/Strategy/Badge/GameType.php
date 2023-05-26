<?php

namespace VideoGamesRecords\CoreBundle\Manager\Strategy\Badge;

use VideoGamesRecords\CoreBundle\Contracts\Strategy\BadgeTypeStrategyInterface;
use VideoGamesRecords\CoreBundle\Entity\Badge;

class GameType extends AbstractBadgeStrategy implements BadgeTypeStrategyInterface
{
    /**
     * @param Badge $badge
     * @return bool
     */
    public function supports(Badge $badge): bool
    {
        return $badge->getType() === self::TYPE_MASTER;
    }

    /**
     * @param Badge $badge
     * @return string
     */
    public function getTitle(Badge $badge): string
    {
        return $badge->getGame()->getName();
    }
}
