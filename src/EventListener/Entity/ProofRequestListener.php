<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Datetime;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\ProofRequest;
use VideoGamesRecords\CoreBundle\Event\ProofEvent;
use VideoGamesRecords\CoreBundle\Event\ProofRequestEvent;
use VideoGamesRecords\CoreBundle\Security\UserProvider;
use VideoGamesRecords\CoreBundle\ValueObject\ProofRequestStatus;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class ProofRequestListener
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
     * @param ProofRequest       $proofRequest
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(ProofRequest $proofRequest, PreUpdateEventArgs $event): void
    {
        $this->changeSet = $event->getEntityChangeSet();
    }

    /**
     * @param ProofRequest       $proofRequest
     * @param LifecycleEventArgs $event
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postPersist(ProofRequest $proofRequest, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();
        $playerChart = $proofRequest->getPlayerChart();
        $playerChart->setStatus(
            $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_DEMAND)
        );
        $em->flush();
    }


     /**
     * @param ProofRequest       $proofRequest
     * @param LifecycleEventArgs $event
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postUpdate(ProofRequest $proofRequest, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();

        $setPlayerResponding = false;
        if (array_key_exists('status', $this->changeSet)) {
            $event = new ProofEvent($proofRequest);
             if ($this->changeSet['status'][0] === ProofRequestStatus::STATUS_IN_PROGRESS && $this->changeSet['status'][1] === ProofRequestStatus::STATUS_ACCEPTED) {
                 $proofRequest->getPlayerChart()->setStatus(
                    $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_INVESTIGATION)
                 );

                 $setPlayerResponding = true;
                 $this->eventDispatcher->dispatch(new ProofRequestEvent($proofRequest), VideoGamesRecordsCoreEvents::PROOF_REQUEST_ACCEPTED);
             }
             if ($this->changeSet['status'][0] === ProofRequestStatus::STATUS_IN_PROGRESS && $this->changeSet['status'][1] === ProofRequestStatus::STATUS_REFUSED) {
                 $proofRequest->getPlayerChart()->setStatus(
                    $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NORMAL)
                 );

                 $setPlayerResponding = true;
                 $this->eventDispatcher->dispatch(new ProofRequestEvent($proofRequest), VideoGamesRecordsCoreEvents::PROOF_REQUEST_REFUSED);
             }
        }

        if ($setPlayerResponding) {
            $proofRequest->setPlayerResponding($this->userProvider->getPlayer());
            $proofRequest->setDateAcceptance(new DateTime());
        }
    }
}
