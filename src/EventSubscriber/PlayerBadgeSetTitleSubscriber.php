<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities as EventPrioritiesAlias;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Contracts\BadgeInterface;
use VideoGamesRecords\CoreBundle\Entity\PlayerBadge;
use VideoGamesRecords\CoreBundle\Manager\BadgeManager;

final class PlayerBadgeSetTitleSubscriber implements EventSubscriberInterface, BadgeInterface
{
    private TranslatorInterface $translator;
    private BadgeManager $badgeManager;

    public function __construct(TranslatorInterface $translator, BadgeManager $badgeManager)
    {
        $this->translator = $translator;
        $this->badgeManager = $badgeManager;
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
    public function setTitle(RequestEvent $event)
    {
        $data = $event->getRequest()->attributes->get('data');
        $method = $event->getRequest()->getMethod();

        if ($method == Request::METHOD_GET && is_array($data) && $data[0] instanceof PlayerBadge) {
            foreach ($data as $playerBadge) {
                $playerBadge->setTitle(
                    sprintf(
                        '%s %s %s',
                        $this->badgeManager->getStrategy($playerBadge->getBadge())->getTitle($playerBadge->getBadge()),
                        $this->translator->trans('badge.earnedOn'),
                        $playerBadge->getCreatedAt()->format('Y-m-d')
                    )
                );
            }
        }
    }
}
