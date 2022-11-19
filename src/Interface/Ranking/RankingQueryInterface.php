<?php
namespace VideoGamesRecords\CoreBundle\Interface\Ranking;

interface RankingQueryInterface
{
    public function getRankingPoints(int $id = null, array $options = []) : array;

    public function getRankingMedals(int $id = null, array $options = []) : array;
}