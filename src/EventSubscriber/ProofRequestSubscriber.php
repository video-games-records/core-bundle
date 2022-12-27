<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\DataTransformer\TokenStorageToPlayerTransformer;
use VideoGamesRecords\CoreBundle\Entity\ProofRequest;
use VideoGamesRecords\CoreBundle\Exception\PostException;
use VideoGamesRecords\CoreBundle\Service\Player\CanAskProofProvider;

final class ProofRequestSubscriber implements EventSubscriberInterface
{

    private TokenStorageInterface $tokenStorage;
    private TranslatorInterface $translator;
    private CanAskProofProvider $canAskProofProvider;
    private TokenStorageToPlayerTransformer $tokenStorageToPlayerTransformer;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        TranslatorInterface $translator,
        CanAskProofProvider $canAskProofProvider,
        TokenStorageToPlayerTransformer $tokenStorageToPlayerTransformer
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->translator = $translator;
        $this->canAskProofProvider = $canAskProofProvider;
        $this->tokenStorageToPlayerTransformer = $tokenStorageToPlayerTransformer;
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
            $player = $this->tokenStorageToPlayerTransformer->transform($this->tokenStorage->getToken());

            if (false === $this->canAskProofProvider->load($player)) {
                 throw new PostException(sprintf($this->translator->trans('proof.request.send.refuse'), CanAskProofProvider::MAX_REQUEST_DAY));
            }

            $request->setPlayerRequesting($player);
        }
    }
}
