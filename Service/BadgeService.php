<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\DBAL\DBALException;
use VideoGamesRecords\CoreBundle\Repository\PlayerBadgeRepository;


class BadgeService
{
    private PlayerBadgeRepository $playerBadgeRepository;

    public function __construct(
        PlayerBadgeRepository $playerBadgeRepository
    ) {
        $this->playerBadgeRepository = $playerBadgeRepository;
    }

    /**
     * @throws DBALException
     */
    public function majUserBadge()
    {
        $this->playerBadgeRepository->majUserBadge();
    }

     /**
     * @throws DBALException
     */
    public function majPlayerBadge()
    {
        $this->playerBadgeRepository->majPlayerBadge();
    }
}
