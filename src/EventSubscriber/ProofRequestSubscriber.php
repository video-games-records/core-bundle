<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Entity\ProofRequest;
use VideoGamesRecords\CoreBundle\Exception\PostException;
use VideoGamesRecords\CoreBundle\Security\UserProvider;
use VideoGamesRecords\CoreBundle\Service\Player\CanAskProofProvider;

final class ProofRequestSubscriber implements EventSubscriberInterface
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
            KernelEvents::VIEW => ['setPlayerRequesting', EventPriorities::POST_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     * @return void
     * @throws PostException
     * @throws ORMException
     */
    public function setPlayerRequesting(ViewEvent $event)
    {
        $request = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (($request instanceof ProofRequest) && ($method == Request::METHOD_POST)) {
            $player = $this->userProvider->getPlayer();

            if (false === $this->canAskProofProvider->load($player)) {
                throw new PostException(
                    sprintf(
                        $this->translator->trans('proof.request.send.refuse'),
                        CanAskProofProvider::MAX_REQUEST_DAY
                    )
                );
            }

            $request->setPlayerRequesting($player);
        }
    }
}
