<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthController extends AbstractController
{
    /**
     * @return array
     */
    public function profile()
    {
        return array(
            $this->getUser()->getRoles(),
            $this->getUser(),
            $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
                ->getPlayerFromUser($this->getUser())
        );
    }


    /**
     * @return mixed
     */
    public function profilePlayer()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
            ->getPlayerFromUser($this->getUser());
    }

    /**
     * @return mixed
     */
    public function profileTeam()
    {
        $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
            ->getPlayerFromUser($this->getUser());
        return $player->getTeam();
    }
}
