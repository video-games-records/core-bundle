<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Repository\LostPositionRepository;

class LostPositionService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws Exception
     */
    public function purge()
    {
        /** @var LostPositionRepository $lostPositionRepository */
        $lostPositionRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:LostPosition');
        $lostPositionRepository->purge();
    }
}
