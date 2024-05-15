<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Game;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Controller\AbtsractFormDataController;

/**
 * Call api for form submit scores
 * Return charts with the one relation player-chart of the connected user
 * If the user has not relation, a default relation is created
 */
class GetFormData extends AbtsractFormDataController
{
    /**
     * @param Game   $game
     * @param Request $request
     * @throws ORMException
     */
    public function __invoke(Game $game, Request $request)
    {
        $this->game = $game;

        $player = $this->userProvider->getPlayer();
        $page = (int) $request->query->get('page', '1');
        $itemsPerPage = (int) $request->query->get('itemsPerPage', '20');
        $locale = $request->getLocale();
        $search = array(
            'game' => $game,
            'term' => $request->query->get('term', null),
        );

        $charts = $this->em->getRepository(Chart::class)->getList(
            $page,
            $player,
            $search,
            $locale,
            $itemsPerPage
        );

        return $this->setScores($charts, $player);
    }
}
