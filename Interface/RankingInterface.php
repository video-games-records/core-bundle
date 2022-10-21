<?php
namespace VideoGamesRecords\CoreBundle\Interface;

interface RankingInterface
{
    public function maj($id): void;

    public function getRankingPoints(int $id, array $options = []): array;

    public function getRankingMedals(int $id, array $options = []): array;
}