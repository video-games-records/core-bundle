<?php
namespace VideoGamesRecords\CoreBundle\Contracts\Ranking;

interface RankingProviderInterface
{
    public function getRankingPoints(int $id = null, array $options = []) : array;

    public function getRankingMedals(int $id = null, array $options = []) : array;
}