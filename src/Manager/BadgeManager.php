<?php

namespace VideoGamesRecords\CoreBundle\Manager;

use VideoGamesRecords\CoreBundle\Contracts\Strategy\BadgeTypeStrategyInterface;
use VideoGamesRecords\CoreBundle\Entity\Badge;

class BadgeManager
{
    /** @var BadgeTypeStrategyInterface[] */
    private $strategies = [];


    public function getStrategy(Badge $badge): BadgeTypeStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($badge)) {
                return $strategy;
            }
        }

        throw new \DomainException(sprintf('Unable to find a strategy to badge type [%s]', $badge->getType()));
    }

    /**
     * @param BadgeTypeStrategyInterface $strategy
     */
    public function addStrategy(BadgeTypeStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }
}
