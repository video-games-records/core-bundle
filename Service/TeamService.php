<?php

namespace VideoGamesRecords\CoreBundle\Service;

use VideoGamesRecords\CoreBundle\Repository\TeamRepository;

class TeamService
{
    private TeamRepository $teamRepository;

    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    /**
     * @return void
     */
    public function majRankBadge()
    {
        $this->teamRepository->majPointBadge();
        $this->teamRepository->majRankBadge();
    }
}
