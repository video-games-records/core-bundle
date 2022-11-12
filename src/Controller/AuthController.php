<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\UserBundle\Service\IpManager;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Team;

class AuthController extends DefaultController
{
    private IpManager $ipManager;
    protected EntityManagerInterface $em;

    /**
     * @param IpManager              $ipManager
     * @param EntityManagerInterface $em
     */
    public function __construct(IpManager $ipManager, EntityManagerInterface $em)
    {
        $this->ipManager = $ipManager;
        parent::__construct($em);
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
