<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Entity\TeamRequest;
use VideoGamesRecords\CoreBundle\Exception\PostException;

final class IsValidTeamRequestSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;

    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['validate', EventPriorities::POST_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     * @throws PostException
     * @throws Exception
     */
    public function validate(ViewEvent $event)
    {
        $requestA = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (($requestA instanceof TeamRequest) && ($method == Request::METHOD_POST)) {
            $requestB = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\TeamRequest')
                ->findOneBy(
                    array(
                        'player' => $requestA->getPlayer(),
                        'status' => TeamRequest::STATUS_ACTIVE
                    )
                );
            if ($requestB) {
                throw new PostException($this->translator->trans('team.request.exists'));
            }
            if ($requestA->getPlayer()->getTeam() != null) {
                throw new PostException($this->translator->trans('team.request.has_team'));
            }
        }
    }
}
