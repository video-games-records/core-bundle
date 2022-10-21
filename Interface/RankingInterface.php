<?php
namespace VideoGamesRecords\CoreBundle\Interface;

interface RankingInterface
{
    public function maj($id): void;

    public function get($id): void;
}