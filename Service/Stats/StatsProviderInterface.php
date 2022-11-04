<?php

namespace VideoGamesRecords\CoreBundle\Service\Stats;

interface StatsProviderInterface
{
    public function load($mixed): array;
}