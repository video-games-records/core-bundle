<?php
namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Events;
use VideoGamesRecords\CoreBundle\Entity\Player;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class DynamicRelationUser implements EventSubscriber
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

        if ($metadata->getName() !== 'ProjetNormandie\UserBundle\Entity\User') {
            return;
        }

        $metadata->mapOneToOne(array(
            'targetEntity' => Player::class,
            'fieldName' => 'player',
            'mappedBy' => 'normandieUser',
        ));
    }
}
