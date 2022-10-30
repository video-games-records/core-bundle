<?php
namespace VideoGamesRecords\CoreBundle\Interface;

interface RankingUpdaterInterface
{
    public function maj(int $id): void;
}