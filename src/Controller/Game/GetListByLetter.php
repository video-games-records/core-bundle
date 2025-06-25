<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Game;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Repository\GameRepository;

class GetListByLetter extends AbstractController
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
        $letter = $request->query->get('letter');
        $locale = $request->getLocale();

        if (null === $letter) {
            throw new \InvalidArgumentException('Letter parameter is required');
        }

        return $this->gameRepository
            ->findWithLetter($letter, $locale)
            ->getResult();
    }
}
