<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\Proof;
use VideoGamesRecords\CoreBundle\ValueObject\ProofStatus;

class ProofListener
{
    /**
     * @param Proof              $proof
     * @param LifecycleEventArgs $event
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postUpdate(proof $proof, LifecycleEventArgs $event): void
    {
        $em = $event->getEntityManager();
        if ($proof->getStatus() == ProofStatus::STATUS_CLOSED) {
            $playerChart = $proof->getPlayerChart();
            if ($playerChart) {
                $playerChart->setProof(null);
                switch ($playerChart->getStatus()->getId()) {
                    case PlayerChartStatus::ID_STATUS_INVESTIGATION:
                    case PlayerChartStatus::ID_STATUS_DEMAND_SEND_PROOF:
                        $playerChart->setStatus(
                            $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_INVESTIGATION)
                        );
                        break;
                    case PlayerChartStatus::ID_STATUS_PROOVED:
                    case PlayerChartStatus::ID_STATUS_NORMAL_SEND_PROOF:
                        $playerChart->setStatus(
                            $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NORMAL)
                        );
                        break;
                }
                $em->flush();
            }
        }
    }
}
