<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Manager\Strategy\Badge;

use VideoGamesRecords\CoreBundle\Contracts\Strategy\BadgeTypeStrategyInterface;
use VideoGamesRecords\CoreBundle\Entity\Badge;

class ValuePrefixType extends AbstractBadgeStrategy implements BadgeTypeStrategyInterface
{
    private array $types = [
        self::TYPE_CONNEXION  => 1,
        self::TYPE_DON => 1,
        self::TYPE_FORUM => 1,
        self::TYPE_VGR_CHART => 1,
        self::TYPE_VGR_PROOF => 1,
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
        return  $badge->getValue() . ' ' . $this->getTranslator()->trans('badge.title.' . $badge->getType());
    }
}
