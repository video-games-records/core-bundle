<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\MessageHandler\Player;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use VideoGamesRecords\CoreBundle\Event\PlayerCountryUpdated;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerCountryRank;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

#[AsMessageHandler]
readonly class UpdatePlayerCountryRankHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(UpdatePlayerCountryRank $updatePlayerCountryRank): void
    {
        $country = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Country')
            ->find($updatePlayerCountryRank->getCountryId());

        $players = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')
            ->findBy(array('country' => $country), array('rankPointChart' => 'ASC'));
        Ranking::addObjectRank($players, 'rankCountry', array('rankPointGame'));
        $this->em->flush();

        $this->eventDispatcher->dispatch(
            new PlayerCountryUpdated($country)
        );
    }
}
