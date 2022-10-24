<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Controller\DefaultController;

/**
 * Class PlayerChartController
 */
class RankingController extends DefaultController
{
    /**
     * @param Request $request
     * @param int     $maxRank
     * @return array
     * @throws ORMException
     */
    protected function getOptions(Request $request, int $maxRank = 5): array
    {
        $idTeam = $request->query->get('idTeam', null);
        return [
            'maxRank' => $request->query->get('maxRank', $maxRank),
            'player' => $this->getPlayer(),
            'team' => $idTeam ? $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $idTeam) : null,
        ];
    }
}
