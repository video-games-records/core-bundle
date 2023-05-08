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

final class PlayerBadgeSetTitleSubscriber implements EventSubscriberInterface, BadgeInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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
        //dd($event->getRequest()->attributes->get('data'));
        $data = $event->getRequest()->attributes->get('data');
        $method = $event->getRequest()->getMethod();

        if ($method == Request::METHOD_GET && is_array($data) && $data[0] instanceof PlayerBadge) {
            foreach($data as $playerBadge) {
                $playerBadge->setTitle($this->getTitle($playerBadge));
            }
        }
    }

    /**
     * @param PlayerBadge $playerBadge
     * @return string
     */
    private function getTitle(PlayerBadge $playerBadge): string
    {
        $badge = $playerBadge->getBadge();
        $titleType = $this->translator->trans('badge.title.' . $badge->getType());
        $title = match (self::TITLES[$badge->getType()]) {
            self::TITLE_PLATFORM => $badge->getPlatform()
                ->getLibPlatform(),
            self::TITLE_GAME => $badge->getGame()
                ->getName(),
            self::TITLE_COUNTRY => $badge->getCountry()
                ->getName(),
            self::TITLE_TYPE_VALUE => $titleType . ' ' . $badge->getValue(),
            self::TITLE_VALUE_TYPE => $badge->getValue() . ' ' . $titleType,
            default => $badge->getType(),
        };

        $title .= ' ' . $this->translator->trans('badge.earnedOn') . ' ' . $playerBadge->getCreatedAt()->format('Y-m-d');

        return $title;
    }
}
