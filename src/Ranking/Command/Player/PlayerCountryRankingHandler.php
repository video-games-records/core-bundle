<?php

namespace VideoGamesRecords\CoreBundle\Ranking\Command\Player;

use Doctrine\ORM\EntityRepository;
use VideoGamesRecords\CoreBundle\Event\CountryEvent;
use VideoGamesRecords\CoreBundle\Ranking\Command\AbstractRankingHandler;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class PlayerCountryRankingHandler extends AbstractRankingHandler
{
    public function handle($mixed): void
    {
        $country = $this->getCountryRepository()->find($mixed);
        if (null === $country) {
            return;
        }

        $players = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')
            ->findBy(array('country' => $country), array('rankPointChart' => 'ASC'));
        Ranking::addObjectRank($players, 'rankCountry', array('rankPointGame'));
        $this->em->flush();

        $event = new CountryEvent($country);
        $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::COUNTRY_MAJ_COMPLETED);
    }

    private function getCountryRepository(): EntityRepository
    {
        return $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Country');
    }
}
