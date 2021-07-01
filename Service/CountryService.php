<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Repository\CountryRepository;

class CountryService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     *
     */
    public function maj()
    {
        /** @var CountryRepository $countryRepository */
        $countryRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Country');
        $countries = $countryRepository->findBy(['boolMaj' => true]);
        foreach ($countries as $country) {
            $this->em->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankCountry($country);
            $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerBadge')->majCountryBadge($country);
            $country->setBoolMaj(false);
        }
        $this->em->flush();
    }
}
