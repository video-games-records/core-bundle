<?php

namespace VideoGamesRecords\CoreBundle\State;

use VideoGamesRecords\CoreBundle\Entity\Twitch;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Metadata\Operation;

final class TwitchGetProvider implements ProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = [])
    {
        return new Twitch($uriVariables['id']);
    }
}