<?php

namespace VideoGamesRecords\CoreBundle\Controller;


use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthController extends AbstractController
{

    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

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
