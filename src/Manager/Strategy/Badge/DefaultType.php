<?php

namespace VideoGamesRecords\CoreBundle\Manager\Strategy\Badge;

use VideoGamesRecords\CoreBundle\Contracts\Strategy\BadgeTypeStrategyInterface;
use VideoGamesRecords\CoreBundle\Entity\Badge;

class DefaultType extends AbstractBadgeStrategy implements BadgeTypeStrategyInterface
{
    private array $types = [
        self::TYPE_TWITCH => 1,
        self::TYPE_INSCRIPTION => 1,
        self::TYPE_SPECIAL_WEBMASTER => 1,
    ];

    /**
     * @param Badge $badge
     * @return bool
     */
    public function supports(Badge $badge): bool
    {
        return array_key_exists($badge->getType(), $this->types);
    }

    /**
     * @param Badge $badge
     * @return string
     */
    public function getTitle(Badge $badge): string
    {
        return $badge->getType();
    }
}
