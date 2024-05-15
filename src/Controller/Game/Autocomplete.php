<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Game;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Repository\GameRepository;

class Autocomplete extends AbstractController
{
    private GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function __invoke(Request $request): mixed
    {
        $q = $request->query->get('query', null);
        $locale = $request->getLocale();
        return $this->gameRepository->autocomplete($q, $locale);
    }
}
