<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;

class PlayerChart
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws ORMException
     */
    public function majInvestigation()
    {
        $list = $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->getPlayerChartToDesactivate();
        $statusReference = $this->em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NOT_PROOVED);
        /** @var \VideoGamesRecords\CoreBundle\Entity\PlayerChart $playerChart */
        foreach ($list as $playerChart) {
            var_dump($playerChart->getId());
            $playerChart->setStatus($statusReference);
        }
        $this->em->flush();
    }
}
