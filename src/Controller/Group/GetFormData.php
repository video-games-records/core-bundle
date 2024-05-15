<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Group;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Controller\AbtsractFormDataController;

/**
 * Call api for form submit scores
 * Return charts with the one relation player-chart of the connected user
 * If the user has not relation, a default relation is created
 */
class GetFormData extends AbtsractFormDataController
{
    /**
     * @param Group   $group
     * @param Request $request
     * @throws ORMException
     */
    public function __invoke(Group $group, Request $request)
    {
        $this->game = $group->getGame();

        $player = $this->userProvider->getPlayer();
        $page = (int) $request->query->get('page', '1');
        $itemsPerPage = (int) $request->query->get('itemsPerPage', '20');
        $locale = $request->getLocale();
        $search = array(
            'group' => $group,
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
