<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Symfony\EventListener\EventPriorities as EventPrioritiesAlias;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartLib;

final class ScoreSetValueSubscriber implements EventSubscriberInterface
{
    public function __construct()
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['setValue', EventPrioritiesAlias::POST_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     */
    public function setValue(ViewEvent $event): void
    {
        $playerChart = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (($playerChart instanceof PlayerChart)
            && in_array($method, array(Request::METHOD_POST, Request::METHOD_PUT))) {
            /** @var PlayerChartLib $lib */
            foreach ($playerChart->getLibs() as $lib) {
                $lib->setValueFromPaseValue();
            }
        }
    }
}
