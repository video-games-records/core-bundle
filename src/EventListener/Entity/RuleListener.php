<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Rule;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

class RuleListener
{
    private UserProvider $userProvider;

    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param Rule $rule
     * @param LifecycleEventArgs $event
     * @return void
     * @throws ORMException
     */
    public function prePersist(Rule $rule, LifecycleEventArgs $event): void
    {
        $rule->setPlayer($this->userProvider->getPlayer());
    }
}
