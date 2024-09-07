<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Manager;

use Doctrine\DBAL\Exception;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Repository\GameRepository;

class GameManager
{
    private GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    /**
     * @param Game $game
     */
    public function maj(Game $game): void
    {
        $this->gameRepository->maj($game);
    }

    /**
     * @param Game $game
     */
    public function copy(Game $game): void
    {
        $this->gameRepository->copy($game);
    }

    public function setProofVideoOnly(Game $game): void
    {

    }
}
