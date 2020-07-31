<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Request as ProofRequest;
use VideoGamesRecords\CoreBundle\Exception\PostException;

final class ProofRequestSubscriber implements EventSubscriberInterface
{

    private $tokenStorage;
    private $em;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em)
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setPlayerRequesting', EventPriorities::POST_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     * @throws RequestLimitException
     * @throws PostException
     */
    public function setPlayerRequesting(ViewEvent $event)
    {
        $request = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (($request instanceof ProofRequest) && ($method == Request::METHOD_POST)) {
            $token = $this->tokenStorage->getToken();
            $player =  $this->em->getRepository('VideoGamesRecordsCoreBundle:Player')
                ->getPlayerFromUser($token->getUser());

            $nbRequest = $this->em->getRepository('VideoGamesRecordsCoreBundle:ProofRequest')->getNbRequestFromToDay($player);
            if ($nbRequest >= 5) {
                throw new PostException('You raise limit request for today');
            }
            $request->setPlayerRequesting($player);
        }
    }
}
