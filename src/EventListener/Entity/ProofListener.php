<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use DateTime;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\Proof;
use VideoGamesRecords\CoreBundle\Event\ProofAccepted;
use VideoGamesRecords\CoreBundle\Event\ProofRefused;
use VideoGamesRecords\CoreBundle\Security\UserProvider;
use VideoGamesRecords\CoreBundle\ValueObject\ProofStatus;

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
    public function preUpdate(Proof $proof, PreUpdateEventArgs $event): void
    {
        $this->changeSet = $event->getEntityChangeSet();
    }

    /**
     * @param Proof $proof
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function postUpdate(Proof $proof, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();
        $event = new ProofAccepted($proof);

        // ACCEPTED
        if ($this->isAccepted()) {
            $proof->getPlayerChart()->setStatus(
                $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_PROOVED)
            );

            $proof->setPlayerResponding($this->userProvider->getPlayer());
            $proof->setCheckedAt(new DateTime());
            $this->eventDispatcher->dispatch(new ProofAccepted($proof));
        }

        // REFUSED
        if ($this->isRefused()) {
            $playerChart = $proof->getPlayerChart();
            if ($playerChart->getStatus()->getId() === PlayerChartStatus::ID_STATUS_PROOVED) {
                $playerChart->setStatus(
                    $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NORMAL)
                );
            } else {
                $idStatus = ($playerChart->getStatus()->getId() === PlayerChartStatus::ID_STATUS_NORMAL_SEND_PROOF)
                   ? PlayerChartStatus::ID_STATUS_NORMAL : PlayerChartStatus::ID_STATUS_INVESTIGATION;
                $playerChart->setStatus($em->getReference(PlayerChartStatus::class, $idStatus));
            }

            $proof->setPlayerResponding($this->userProvider->getPlayer());
            $proof->setCheckedAt(new DateTime());
            $this->eventDispatcher->dispatch(new ProofRefused($proof));
        }

        if ($proof->getStatus()->getValue() == ProofStatus::CLOSED) {
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


    private function isAccepted(): bool
    {
        return array_key_exists('status', $this->changeSet)
            && $this->changeSet['status'][0] === ProofStatus::IN_PROGRESS
            && $this->changeSet['status'][1] === ProofStatus::ACCEPTED;
    }

    private function isRefused(): bool
    {
        return array_key_exists('status', $this->changeSet)
            && in_array(
                $this->changeSet['status'][0],
                array(ProofStatus::IN_PROGRESS, ProofStatus::ACCEPTED)
            )
            && $this->changeSet['status'][1] === ProofStatus::REFUSED;
    }
}
