<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Game;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Manager\GameOfDayManager;
use VideoGamesRecords\CoreBundle\Entity\Game;

class GetGameOfDay extends AbstractController
{
    public function __construct(private readonly GameOfDayManager $gameOfDayManager)
    {
    }

    /**
     * @param Request $request
     * @return Game
     */
    public function __invoke(Request $request): Game
    {
        // Force regeneration si le paramètre refresh est présent
        if ($request->query->has('refresh')) {
            $game = $this->gameOfDayManager->regenerateGameOfDay();
        } else {
            $game = $this->gameOfDayManager->getGameOfDay();
        }

        return $game;
    }
}
