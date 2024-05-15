<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Contracts\Ranking;

interface RankingCommandInterface
{
    public function handle($mixed): void;
}
