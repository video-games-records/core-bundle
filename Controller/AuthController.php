<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthController extends AbstractController
{
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
    public function profile()
    {
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
