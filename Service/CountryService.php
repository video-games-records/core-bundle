<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use VideoGamesRecords\CoreBundle\Repository\CountryRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerBadgeRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;

class CountryService
{
    private CountryRepository $countryRepository;
    private PlayerRepository $playerRepository;
    private PlayerBadgeRepository $playerBadgeRepository;

    public function __construct(
        CountryRepository $countryRepository,
        PlayerRepository $playerRepository,
        PlayerBadgeRepository $playerBadgeRepository
    )
    {
        $this->countryRepository = $countryRepository;
        $this->playerRepository = $playerRepository;
        $this->playerBadgeRepository = $playerBadgeRepository;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function maj()
    {
        $countries = $this->countryRepository->findBy(['boolMaj' => true]);
        foreach ($countries as $country) {
            $this->playerRepository->majRankCountry($country);
            $this->playerBadgeRepository->majCountryBadge($country);
            $country->setBoolMaj(false);
        }
        $this->countryRepository->flush();
    }
}
