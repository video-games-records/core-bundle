<?php

namespace VideoGamesRecords\CoreBundle\Manager\Strategy\Badge;

use VideoGamesRecords\CoreBundle\Contracts\Strategy\BadgeTypeStrategyInterface;
use VideoGamesRecords\CoreBundle\Entity\Badge;

class ValueSuffixType extends AbstractBadgeStrategy implements BadgeTypeStrategyInterface
{
    private array $types = [
        self::TYPE_VGR_SPECIAL_CUP => 1,
        self::TYPE_VGR_SPECIAL_LEGEND => 1,
        self::TYPE_VGR_SPECIAL_MEDALS => 1,
        self::TYPE_VGR_SPECIAL_POINTS => 1,
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
        return $this->getTranslator()->trans('badge.title.' . $badge->getType()) . ' ' . $badge->getValue();
    }
}
