<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities as EventPrioritiesAlias;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Contracts\VgrCoreInterface;
use VideoGamesRecords\CoreBundle\Entity\ProofRequest;
use VideoGamesRecords\CoreBundle\Exception\PostException;
use VideoGamesRecords\CoreBundle\Security\UserProvider;
use VideoGamesRecords\CoreBundle\DataProvider\CanAskProofProvider;

final class CanAskProofSubscriber implements EventSubscriberInterface, VgrCoreInterface
{
    private TranslatorInterface $translator;
    private CanAskProofProvider $canAskProofProvider;
    private UserProvider $userProvider;

    public function __construct(
        TranslatorInterface $translator,
        CanAskProofProvider $canAskProofProvider,
        UserProvider $userProvider
    ) {
        $this->translator = $translator;
        $this->canAskProofProvider = $canAskProofProvider;
        $this->userProvider = $userProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['setPlayerRequesting', EventPrioritiesAlias::POST_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     * @return void
     * @throws PostException
     * @throws ORMException
     */
    public function setPlayerRequesting(ViewEvent $event): void
    {
        $request = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (($request instanceof ProofRequest) && ($method == Request::METHOD_POST)) {
            $player = $this->userProvider->getPlayer();

            if (false === $this->canAskProofProvider->load($player)) {
                throw new PostException(
                    sprintf(
                        $this->translator->trans('proof.request.send.refuse'),
                        self::MAX_PROOF_REQUEST_DAY
                    )
                );
            }

            $request->setPlayerRequesting($player);
        }
    }
}
