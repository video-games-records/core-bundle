<?php

namespace VideoGamesRecords\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use VideoGamesRecords\CoreBundle\EventListener\Entity\DynamicRelationBadge;
use Doctrine\ORM\Events;

class VideoGamesRecordsCoreBundle extends Bundle
{

    public function boot()
    {
        // get the doctrine 2 entity manager
        /*$em = $this->container->get('doctrine.orm.default_entity_manager');

        // get the event manager
        $evm = $em->getEventManager();

        // create and then add our event!
        $inheritableEntityEvent = new DynamicRelationBadge();
        $evm->addEventListener(Events::loadClassMetadata, $inheritableEntityEvent);*/
    }
}
