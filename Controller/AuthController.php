<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthController extends AbstractController
{
    public function profile()
    {
        return array(
            $this->getUser()->getRoles(),
            $this->getUser(),
            $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
                ->getPlayerFromUser($this->getUser())
        );
    }
}
