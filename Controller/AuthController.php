<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use ProjetNormandie\UserBundle\Service\IpManager;

class AuthController extends AbstractController
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
     * @return mixed
     */
    private function getPlayer()
    {
        return  $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
            ->getPlayerFromUser($this->getUser());
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
     * @return mixed
     */
    public function profilePlayer()
    {
        return $this->getPlayer();
    }

    /**
     * @return mixed
     */
    public function profileTeam()
    {
        return $this->getPlayer()->getTeam();
    }
}
