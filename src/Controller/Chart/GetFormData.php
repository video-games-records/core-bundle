<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Chart;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Controller\AbtsractFormDataController;

/**
 * Call api for form submit scores
 * Return charts with the one relation player-chart of the connected user
 * If the user has not relation, a default relation is created
 */
class GetFormData extends AbtsractFormDataController
{
    /**
     * @param Chart   $chart
     * @param Request $request
     * @return mixed
     * @throws ORMException
     */
    public function __invoke(Chart $chart, Request $request): mixed
    {
        $this->game = $chart->getGroup()->getGame();

        $player = $this->userProvider->getPlayer();
        $page = 1;
        $itemsPerPage = 20;
        $locale = $request->getLocale();
        $search = array(
            'chart' => $chart,
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
