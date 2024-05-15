<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Contracts\Ranking;

interface RankUpdateInterface
{
    public function majRankPointChart(): void;

    public function majRankPointGame(): void;

    public function majRankMedal(): void;

    public function majRankCup(): void;

    public function majRankProof(): void;

    public function majRankBadge(): void;
}
