<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\ProofRequest;

class ProofRequestListener
{
    /**
     * @param ProofRequest       $proofRequest
     * @param LifecycleEventArgs $event
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postPersist(ProofRequest $proofRequest, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        $playerChart = $proofRequest->getPlayerChart();
        $playerChart->setStatus(
            $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_DEMAND)
        );
        $em->flush();
    }
}
