<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Entity\ProofRequest;
use VideoGamesRecords\CoreBundle\Exception\PostException;

final class ProofRequestSubscriber implements EventSubscriberInterface
{

    private TokenStorageInterface $tokenStorage;
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em,  TranslatorInterface $translator)
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setPlayerRequesting', EventPriorities::POST_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     * @throws PostException
     */
    public function setPlayerRequesting(ViewEvent $event)
    {
        $request = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        $nb = 3;

        if (($request instanceof ProofRequest) && ($method == Request::METHOD_POST)) {
            $token = $this->tokenStorage->getToken();
            $player =  $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')
                ->getPlayerFromUser($token->getUser());

            $nbRequest = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\ProofRequest')->getNbRequestFromToDay($player);
            if ($nbRequest >= $nb) {
                 throw new PostException(sprintf($this->translator->trans('proof.request.send.refuse'), $nb));
            }
            $request->setPlayerRequesting($player);
        }
    }
}
