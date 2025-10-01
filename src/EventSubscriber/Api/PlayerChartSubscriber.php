<?php

// src/EventSubscriber/Api/PlayerChartSubcriber
namespace VideoGamesRecords\CoreBundle\EventSubscriber\Api;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
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
    private EntityManagerInterface $entityManager;

    public function __construct(MessageBusInterface $messageBus, EntityManagerInterface $entityManager)
    {
        $this->messageBus = $messageBus;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['onPlayerChartPreWrite', EventPriorities::PRE_WRITE],
                ['onPlayerChartPostWrite', EventPriorities::POST_WRITE],
            ],
        ];
    }

    public function onPlayerChartPreWrite(ViewEvent $event): void
    {
        $controllerResult = $event->getControllerResult();
        $request = $event->getRequest();

        // Vérifier si c'est une entité PlayerChart et une requête PUT
        if (!$controllerResult instanceof PlayerChart) {
            return;
        }

        $method = $request->getMethod();
        if ($method !== Request::METHOD_PUT && $method !== Request::METHOD_POST) {
            return;
        }

        $controllerResult->setLastUpdate(new \DateTime());
        $controllerResult->setProof(null);
        $defaultStatus = $this->entityManager->getRepository(PlayerChartStatus::class)->find(1);
        if ($defaultStatus) {
            //$controllerResult->setStatus($defaultStatus);
        }

        // Mettre à jour le game.lastScore
        $game = $controllerResult->getChart()->getGroup()->getGame();
        $game->setLastScore($controllerResult);
    }

    public function onPlayerChartPostWrite(ViewEvent $event): void
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
