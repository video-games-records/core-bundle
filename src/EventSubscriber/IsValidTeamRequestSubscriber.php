<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities as EventPrioritiesAlias;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Entity\TeamRequest;
use VideoGamesRecords\CoreBundle\Exception\PostException;
use VideoGamesRecords\CoreBundle\Security\UserProvider;
use VideoGamesRecords\CoreBundle\ValueObject\TeamRequestStatus;

final class IsValidTeamRequestSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;

    private UserProvider $userProvider;

    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator, UserProvider $userProvider)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->userProvider = $userProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['validate', EventPrioritiesAlias::POST_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     * @throws PostException
     * @throws Exception
     */
    public function validate(ViewEvent $event)
    {
        $data = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        $player = $this->userProvider->getPlayer();

        if ($player && ($data instanceof TeamRequest) && ($method == Request::METHOD_POST)) {
            $teamRequest = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\TeamRequest')
                ->findOneBy(
                    array(
                        'player' => $player,
                        'status' => TeamRequestStatus::ACTIVE
                    )
                );
            if ($player->getTeam() != null) {
                throw new BadRequestException($this->translator->trans('team.request.has_team'));
            }
            if ($teamRequest) {
                throw new BadRequestException($this->translator->trans('team.request.exists'));
            }
        }
    }
}
