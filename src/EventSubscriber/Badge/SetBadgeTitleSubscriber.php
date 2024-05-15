<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber\Badge;

use ApiPlatform\Symfony\EventListener\EventPriorities as EventPrioritiesAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Contracts\BadgeInterface;
use VideoGamesRecords\CoreBundle\Entity\PlayerBadge;
use VideoGamesRecords\CoreBundle\Entity\TeamBadge;
use VideoGamesRecords\CoreBundle\Manager\BadgeManager;

final class SetBadgeTitleSubscriber implements EventSubscriberInterface, BadgeInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        #[Autowire(service: 'vgr.core.manager.badge')]
        private readonly BadgeManager $badgeManager
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['setTitle', EventPrioritiesAlias::POST_READ],
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function setTitle(RequestEvent $event): void
    {
        $data = $event->getRequest()->attributes->get('data');
        $method = $event->getRequest()->getMethod();

        if (
            $method == Request::METHOD_GET
            && is_array($data)
            && count($data) > 0
            && ($data[0] instanceof PlayerBadge || $data[0] instanceof TeamBadge)
        ) {
            foreach ($data as $userBadge) {
                $userBadge->getBadge()->setTitle(
                    sprintf(
                        '%s %s %s',
                        $this->badgeManager->getStrategy($userBadge->getBadge())->getTitle($userBadge->getBadge()),
                        $this->translator->trans('badge.earnedOn'),
                        $userBadge->getCreatedAt()->format('Y-m-d')
                    )
                );
            }
        }
    }
}
