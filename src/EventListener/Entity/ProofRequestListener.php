<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Datetime;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\ProofRequest;
use VideoGamesRecords\CoreBundle\Event\ProofRequestAccepted;
use VideoGamesRecords\CoreBundle\Event\ProofRequestRefused;
use VideoGamesRecords\CoreBundle\Security\UserProvider;
use VideoGamesRecords\CoreBundle\ValueObject\ProofRequestStatus;

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
     * @param ProofRequest $proofRequest
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function postUpdate(ProofRequest $proofRequest, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();

        if ($this->isAccepted()) {
            $proofRequest->getPlayerChart()->setStatus(
                $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_INVESTIGATION)
            );

            $proofRequest->setPlayerResponding($this->userProvider->getPlayer());
            $proofRequest->setDateAcceptance(new DateTime());
            $this->eventDispatcher->dispatch(new ProofRequestAccepted($proofRequest));
        }

        if ($this->isRefused()) {
            $proofRequest->getPlayerChart()->setStatus(
                $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NORMAL)
            );

            $proofRequest->setPlayerResponding($this->userProvider->getPlayer());
            $proofRequest->setDateAcceptance(new DateTime());
            $this->eventDispatcher->dispatch(new ProofRequestRefused($proofRequest));
        }
    }

    private function isAccepted(): bool
    {
        return array_key_exists('status', $this->changeSet)
           && $this->changeSet['status'][0] === ProofRequestStatus::IN_PROGRESS
           && $this->changeSet['status'][1] === ProofRequestStatus::ACCEPTED;
    }

    private function isRefused(): bool
    {
        return array_key_exists('status', $this->changeSet)
            && $this->changeSet['status'][0] === ProofRequestStatus::IN_PROGRESS
            && $this->changeSet['status'][1] === ProofRequestStatus::REFUSED;
    }
}
