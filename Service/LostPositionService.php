<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\DBAL\Exception;
use VideoGamesRecords\CoreBundle\Repository\LostPositionRepository;

class LostPositionService
{
    private LostPositionRepository $lostPositionRepository;

    public function __construct(LostPositionRepository $lostPositionRepository)
    {
        $this->lostPositionRepository = $lostPositionRepository;
    }

    /**
     * @throws Exception
     */
    public function purge()
    {
        $this->lostPositionRepository->purge();
    }
}
