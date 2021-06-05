<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;

final class LostPositionSubscriber implements EventSubscriberInterface
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
            KernelEvents::VIEW => ['setLastDisplayLostPosition', EventPriorities::POST_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     */
    public function setLastDisplayLostPosition(ViewEvent $event)
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
                $player = $this->em->getRepository('VideoGamesRecordsCoreBundle:Player')
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
