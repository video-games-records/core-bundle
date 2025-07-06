<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\MessageHandler\Player;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Event\PlayerPlatformUpdated;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerPlatformRank;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

#[AsMessageHandler]
readonly class UpdatePlayerPlatformRankHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @throws ORMException
     * @throws ExceptionInterface
     */
    public function __invoke(UpdatePlayerPlatformRank $updatePlayerPlatformRank): void
    {
        $platform = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Platform')->find(
            $updatePlayerPlatformRank->getPlatformId()
        );
        if (null === $platform) {
            return;
        }

        // Delete old data
        $query = $this->em->createQuery(
            'DELETE VideoGamesRecords\CoreBundle\Entity\PlayerPlatform pp WHERE pp.platform = :platform'
        );
        $query->setParameter('platform', $platform);
        $query->execute();

        // Select data
        $query = $this->em->createQuery("
            SELECT
                p.id,
                ifnull(SUM(pc.pointPlatform), 0) as pointPlatform,
                COUNT(pc) as nbChart
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            JOIN pc.platform pl
            WHERE pl.id = :idPlatform
            GROUP BY p.id
            ORDER BY pointPlatform DESC");

        $query->setParameter('idPlatform', $platform->getId());
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $list[] = $row;
        }

        $list = Ranking::addRank($list, 'rankPointPlatform', ['pointPlatform']);
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        foreach ($list as $row) {
            $playerPlatform = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\PlayerPlatform'
            );
            $playerPlatform->setPlayer(
                $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $row['id'])
            );
            $playerPlatform->setPlatform($platform);

            $this->em->persist($playerPlatform);
            $this->em->flush();
        }

        $this->eventDispatcher->dispatch(
            new PlayerPlatformUpdated($platform)
        );
    }
}
