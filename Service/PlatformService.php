<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use ProjetNormandie\ForumBundle\Entity\Topic;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;

class PlatformService
{
    private EntityManagerInterface $em;

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
     * @param $platform
     * @return Platform
     */
    private function getPlatform($platform): Platform
    {
        if (!$platform instanceof Platform) {
            $platform = $this->em->getRepository('VideoGamesRecordsCoreBundle:Platform')
                ->findOneBy(['id' => $platform]);
        }
        return $platform;
    }


    /**
     *
     */
    public function majAll()
    {
        $platforms = $this->em->getRepository('VideoGamesRecordsCoreBundle:Platform')->findAll();
        foreach ($platforms as $platform) {
            $this->majRanking($platform);
        }
    }

    /**
     * @param $platform
     */
    public function majRanking($platform)
    {
        $platform = $this->getPlatform($platform);
        if ($platform) {
            // Ranking
            $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerPlatform')->maj($platform);
            // Badge
            $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')->majPlatformBadge($platform);
        }
    }
}
