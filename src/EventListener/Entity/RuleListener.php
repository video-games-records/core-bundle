<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;
use VideoGamesRecords\CoreBundle\Entity\Rule;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

class RuleListener
{
    public function __construct(private UserProvider $userProvider, private RequestStack $requestStack)
    {
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

    /**
     * @param Rule $rule
     * @param LifecycleEventArgs $event
     */
    public function postLoad(Rule $rule, LifecycleEventArgs $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $rule->setCurrentLocale($request->getLocale());
        }
    }
}
