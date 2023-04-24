<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Player;

class AuthController extends AbstractController
{
    protected EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function profile(): array
    {
        return array(
            $this->getUser()->getRoles(),
            $this->getUser(),
            $this->profilePlayer()
        );
    }

    /**
     * @return Player|null
     */
    public function profilePlayer(): ?Player
    {
        if ($this->getUser() !== null) {
            return $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')
                ->getPlayerFromUser($this->getUser());
        }
        return null;
    }
}
