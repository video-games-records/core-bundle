<?php
namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Events;
use VideoGamesRecords\CoreBundle\Entity\Game;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class DynamicRelationBadge implements EventSubscriber
{
    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        // the $metadata is the whole mapping info for this class
        $metadata = $eventArgs->getClassMetadata();

        if ($metadata->getName() !== 'ProjetNormandie\BadgeBundle\Entity\Badge') {
            return;
        }

        $metadata->mapOneToMany(array(
            'targetEntity' => Game::class,
            'fieldName' => 'games',
            'mappedBy' => 'badge',
        ));
    }
}
