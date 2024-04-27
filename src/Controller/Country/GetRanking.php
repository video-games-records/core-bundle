<?php

namespace VideoGamesRecords\CoreBundle\Controller\Country;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Country;
use VideoGamesRecords\CoreBundle\Ranking\Provider\Player\PlayerRankingProvider;


class GetRanking extends AbstractController
{

    public function __construct(
        private readonly PlayerRankingProvider $playerRankingProvider)
    {
    }

    /**
     * @param Country $country
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function __invoke(Country $country, Request $request): array
    {
        return $this->playerRankingProvider->getRankingCountry(
            $country,
            [
                'maxRank' => $request->query->get('maxRank', '5'),
                'idTeam' => $request->query->get('idTeam'),
                'limit' => $request->query->get('limit')
            ]
        );
    }
}
