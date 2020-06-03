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
            $this->getUser()->getRelation()
        );
    }


    /**
     * @return mixed
     */
    public function profilePlayer()
    {
        return $this->getUser()->getRelation();
    }

    /**
     * @return mixed
     */
    public function profileTeam()
    {
        $player = $this->getUser()->getRelation();
        return $player->getTeam();
    }
}
