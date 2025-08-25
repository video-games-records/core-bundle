<?php

namespace VideoGamesRecords\CoreBundle\EventSubscriber\Api;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerChartRank;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;
use Zenstruck\Messenger\Monitor\Stamp\DescriptionStamp;

class PlayerChartSubscriber implements EventSubscriberInterface
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onPlayerChartUpdate', EventPriorities::POST_WRITE],
        ];
    }

    public function onPlayerChartUpdate(ViewEvent $event): void
    {
        $controllerResult = $event->getControllerResult();
        $request = $event->getRequest();

        // Vérifier si c'est une entité PlayerChart et une requête PUT ou POST
        if (!$controllerResult instanceof PlayerChart) {
            return;
        }

        $method = $request->getMethod();
        if ($method !== Request::METHOD_PUT && $method !== Request::METHOD_POST) {
            return;
        }

        // Dispatcher le message UpdatePlayerChartRank
        $message = new UpdatePlayerChartRank($controllerResult->getChart()->getId());
        $this->messageBus->dispatch(
            $message,
            [
                new DescriptionStamp(
                    sprintf('Update player-ranking for chart [%d]', $controllerResult->getChart()->getId())
                )
            ]
        );
    }
}
