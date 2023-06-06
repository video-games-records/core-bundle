<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use DateTime;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\Proof;
use VideoGamesRecords\CoreBundle\Event\ProofEvent;
use VideoGamesRecords\CoreBundle\Security\UserProvider;
use VideoGamesRecords\CoreBundle\ValueObject\ProofStatus;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class ProofListener
{
    private array $changeSet = array();
    private UserProvider $userProvider;
    private EventDispatcherInterface $eventDispatcher;


    public function __construct(UserProvider $userProvider, EventDispatcherInterface $eventDispatcher)
    {
        $this->userProvider = $userProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Proof              $proof
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(proof $proof, PreUpdateEventArgs $event): void
    {
        $this->changeSet = $event->getEntityChangeSet();
    }

    /**
     * @param Proof              $proof
     * @param LifecycleEventArgs $event
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postUpdate(proof $proof, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();

        $setPlayerResponding = false;
        if (array_key_exists('status', $this->changeSet)) {
            $event = new ProofEvent($proof);
            if ($this->changeSet['status'][0] === ProofStatus::STATUS_IN_PROGRESS && $this->changeSet['status'][1] === ProofStatus::STATUS_ACCEPTED) {
                $proof->getPlayerChart()->setStatus(
                    $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_PROOVED)
                );

                $setPlayerResponding = true;
                $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::PROOF_ACCEPTED);
            }
            if (in_array($this->changeSet['status'][0], array(ProofStatus::STATUS_IN_PROGRESS, ProofStatus::STATUS_ACCEPTED)
                ) && $this->changeSet['status'][1] === ProofStatus::STATUS_REFUSED) {
                $playerChart = $proof->getPlayerChart();
                if ($playerChart->getStatus()->getId() === PlayerChartStatus::ID_STATUS_PROOVED) {
                    $playerChart->setStatus($em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NORMAL));
                } else {
                    $idStatus = ($playerChart->getStatus()->getId() === PlayerChartStatus::ID_STATUS_NORMAL_SEND_PROOF)
                       ? PlayerChartStatus::ID_STATUS_NORMAL : PlayerChartStatus::ID_STATUS_INVESTIGATION;
                    $playerChart->setStatus($em->getReference(PlayerChartStatus::class, $idStatus));
                }

                $setPlayerResponding = true;
                $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::PROOF_REFUSED);
            }
        }

        if ($setPlayerResponding) {
            $proof->setPlayerResponding($this->userProvider->getPlayer());
            $proof->setCheckedAt(new DateTime());
        }


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
