<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use ProjetNormandie\ForumBundle\Entity\Topic;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\PlayerPlatform;
use VideoGamesRecords\CoreBundle\Repository\PlatformRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerBadgeRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerPlatformRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;

class PlatformService
{
    private PlatformRepository $platformRepository;
    private PlayerPlatformRepository $playerPlatformRepository;
    private PlayerBadgeRepository $playerBadgeRepository;

    public function __construct(
        PlatformRepository $platformRepository,
        PlayerPlatformRepository $playerPlatformRepository,
        PlayerBadgeRepository $playerBadgeRepository
    ) {
        $this->platformRepository = $platformRepository;
        $this->playerPlatformRepository = $playerPlatformRepository;
        $this->playerBadgeRepository = $playerBadgeRepository;
    }

    /**
     * @param $platform
     * @return Platform
     */
    private function getPlatform($platform): Platform
    {
        if (!$platform instanceof Platform) {
            $platform = $this->platformRepository->findOneBy(['id' => $platform]);
        }
        return $platform;
    }



    /**
     * @param $platform
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function majRanking($platform)
    {
        $platform = $this->getPlatform($platform);
        if ($platform) {
            // Ranking
            $this->playerPlatformRepository->maj($platform);
            // Badge
            $this->playerBadgeRepository->majPlatformBadge($platform);
        }
    }
}
