<?php

namespace VideoGamesRecords\CoreBundle\Handler\Ranking\Player;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Event\PlatformEvent;
use VideoGamesRecords\CoreBundle\Handler\Ranking\AbstractRankingHandler;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class PlayerPlatformRankingHandler extends AbstractRankingHandler
{
    /*public function majAll()
    {
        $platforms = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Platform')->findAll();
        foreach ($platforms as $platform) {
            $this->handle($platform->getId());
        }
    }*/

    public function handle($mixed): void
    {
        $platform = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Platform')->find($mixed);
        if (null === $platform) {
            return;
        }
        
        // Delete old data
        $query = $this->em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\PlayerPlatform pp WHERE pp.platform = :platform');
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
                $row, 'VideoGamesRecords\CoreBundle\Entity\PlayerPlatform'
            );
            $playerPlatform->setPlayer($this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $row['id']));
            $playerPlatform->setPlatform($platform);

            $this->em->persist($playerPlatform);
            $this->em->flush();
        }

        $event = new PlatformEvent($platform);
        $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::PLATFORM_MAJ_COMPLETED);
    }
}
