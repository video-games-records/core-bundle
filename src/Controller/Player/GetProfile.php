<?php

namespace VideoGamesRecords\CoreBundle\Controller\Player;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;

class GetProfile extends AbstractController
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
     * @return mixed|Player|null
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function __invoke(): mixed
    {
        if ($this->getUser() !== null) {
            return $this->playerRepository->getPlayerFromUser($this->getUser());
        }
        return null;
    }
}
