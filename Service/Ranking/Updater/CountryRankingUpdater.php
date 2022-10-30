<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Updater;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use VideoGamesRecords\CoreBundle\Event\CountryEvent;
use VideoGamesRecords\CoreBundle\Interface\RankingUpdaterInterface;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class CountryRankingUpdater implements RankingUpdaterInterface
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function majAll()
    {
        $countries = $this->getCountryRepository()->findBy(['boolMaj' => true]);
        foreach ($countries as $country) {
            $this->maj($country->getId());
            $country->setBoolMaj(false);
        }
        $this->em->flush();
    }

    public function maj(int $id): void
    {
        $country = $this->getCountryRepository()->find($id);
        if (null === $country) {
            return;
        }

        $players = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')
            ->findBy(array('country' => $country), array('rankPointChart' => 'ASC'));
        Ranking::addObjectRank($players, 'rankCountry', array('rankPointChart'));
        $this->em->flush();

        $event = new CountryEvent($country);
        $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::COUNTRY_MAJ_COMPLETED);
    }

    private function getCountryRepository(): EntityRepository
    {
        return $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Country');
    }
}
