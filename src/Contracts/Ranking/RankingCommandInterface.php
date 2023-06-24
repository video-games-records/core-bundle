<?php
namespace VideoGamesRecords\CoreBundle\Contracts\Ranking;

interface RankingCommandInterface
{
    public function handle($mixed): void;
}
