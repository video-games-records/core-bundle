<?php
namespace VideoGamesRecords\CoreBundle\Interface;

interface RankingInterface
{
    public function maj(int $id): void;

    public function getRankingPoints(int $id = null, array $options = []): array;

    public function getRankingMedals(int $id = null, array $options = []): array;
}