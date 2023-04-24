<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Team;

class AuthController extends DefaultController
{
    protected EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
    }

    /**
     * @return array
     */
    public function profile(): array
    {
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
