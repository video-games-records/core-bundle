<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @deprecated
 */
final class LostPositionSubscriber implements EventSubscriberInterface
{

    private TokenStorageInterface $tokenStorage;
    private EntityManagerInterface $em;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em)
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['setLastDisplayLostPosition', EventPriorities::POST_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     */
    public function setLastDisplayLostPosition(ViewEvent $event): void
    {
        $attributes = RequestAttributesExtractor::extractAttributes($event->getRequest());
        $method = $event->getRequest()->getMethod();
        if (
            array_key_exists('resource_class', $attributes)
            && ($attributes['resource_class'] == 'VideoGamesRecords\CoreBundle\Entity\LostPosition')
            && ($method == Request::METHOD_GET)
            && isset($attributes['collection_operation_name'])
            && ($attributes['collection_operation_name'] == 'get')
        ) {
            $token = $this->tokenStorage->getToken();
            if ($token->getUser() != 'anon.') {
                // MAJ date see lost position
                $player = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')
                    ->findOneBy(
                        [
                            'id' => $token->getUser()->getId(),
                        ]
                    );
                $player->setLastDisplayLostPosition(new \DateTime());
                $this->em->flush();
            }
        }
    }
}
