<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use ProjetNormandie\UserBundle\Service\IpManager;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Team;

class AuthController extends DefaultController
{
    private IpManager $ipManager;

    /**
     * @param IpManager    $ipManager
     */
    public function __construct(IpManager $ipManager)
    {
        $this->ipManager = $ipManager;
    }

    /**
     * @return array
     */
    public function profile(): array
    {
        $this->ipManager->majUserIp($this->getUser());
        return array(
            $this->getUser()->getRoles(),
            $this->getUser(),
            $this->getPlayer()
        );
    }

    /**
     * @return Player|null
     */
    public function profilePlayer(): ?Player
    {
        return $this->getPlayer();
    }

    /**
     * @return Team|null
     */
    public function profileTeam(): ?Team
    {
        return $this->getTeam();
    }
}
