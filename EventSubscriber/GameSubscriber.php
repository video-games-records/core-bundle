<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;
use VideoGamesRecords\CoreBundle\Event\GameEvent;

final class GameSubscriber implements EventSubscriberInterface
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em,)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::GAME_MAJ_COMPLETED => 'postMaj',
        ];
    }

    /**
     * @param GameEvent $event
     */
    public function postMaj(GameEvent $event)
    {
        var_dump($event->getGame()->getLibGameFr());
    }
}
