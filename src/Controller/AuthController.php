<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;

class AuthController extends AbstractController
{
    protected PlayerRepository $playerRepository;

    /**
     * @param PlayerRepository $playerRepository
     */
    public function __construct(PlayerRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }


    /**
     * @return null
     */
    public function profile()
    {
        if ($this->getUser() !== null) {
            return $this->playerRepository->getPlayerFromUserId($this->getUser()->getId());
        }
        return null;
    }
}
