<?php
namespace VideoGamesRecords\CoreBundle\Interface;

interface RankingSelectInterface
{
    public function getRankingPoints(int $id = null, array $options = []) : array;

    public function getRankingMedals(int $id = null, array $options = []) : array;

}