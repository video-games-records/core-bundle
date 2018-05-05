<?php

namespace VideoGamesRecords\CoreBundle\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;

class LoginListener
{
    /** @var \VideoGamesRecords\CoreBundle\Repository\PlayerRepository */
    private $playerRepository;

    /**
     * @param \VideoGamesRecords\CoreBundle\Repository\PlayerRepository $repository
     */
    public function __construct(PlayerRepository $repository)
    {
        $this->playerRepository = $repository;
    }

    /**
     * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        if ($event->getAuthenticationToken()->isAuthenticated()) {
            /** @var \AppBundle\Entity\User $user */
            $user = $event->getAuthenticationToken()->getUser();
            $player = $this->playerRepository->getPlayerFromUser($user);
            $session = $event->getRequest()->getSession();

            $session->set('vgr_player', $player);
        }
    }
}
