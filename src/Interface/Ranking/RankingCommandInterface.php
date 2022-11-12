<?php
namespace VideoGamesRecords\CoreBundle\Interface\Ranking;

interface RankingCommandInterface
{
    public function handle($mixed): void;
}