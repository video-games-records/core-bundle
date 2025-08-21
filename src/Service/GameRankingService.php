<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Service;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\GameTopRanking;
use VideoGamesRecords\CoreBundle\Repository\GameTopRankingRepository;

class GameRankingService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EntityManagerInterface $dwhEntityManager,
        private GameTopRankingRepository $rankingRepository
    ) {}

    /**
     * Generate weekly rankings
     */
    public function generateWeeklyRankings(?int $year = null, ?int $week = null): array
    {
        $date = new DateTime();
        if ($year && $week) {
            $date->setISODate($year, $week);
        }

        $currentYear = (int) $date->format('Y');
        $currentWeek = (int) $date->format('W');
        $periodValue = sprintf('%d-W%02d', $currentYear, $currentWeek);

        // Get previous week for position comparison
        $prevDate = clone $date;
        $prevDate->modify('-1 week');
        $prevYear = (int) $prevDate->format('Y');
        $prevWeek = (int) $prevDate->format('W');

        return $this->generateRankingsForPeriod(
            GameTopRanking::PERIOD_WEEK,
            $periodValue,
            $prevYear,
            $prevWeek
        );
    }

    /**
     * Generate monthly rankings
     */
    public function generateMonthlyRankings(?int $year = null, ?int $month = null): array
    {
        $date = new DateTime();
        if ($year && $month) {
            $date->setDate($year, $month, 1);
        }

        $currentYear = (int) $date->format('Y');
        $currentMonth = (int) $date->format('n');
        $periodValue = sprintf('%d-%02d', $currentYear, $currentMonth);

        // Get previous month for position comparison
        $prevDate = clone $date;
        $prevDate->modify('-1 month');
        $prevYear = (int) $prevDate->format('Y');
        $prevMonth = (int) $prevDate->format('n');

        return $this->generateRankingsForPeriod(
            GameTopRanking::PERIOD_MONTH,
            $periodValue,
            $prevYear,
            $prevMonth
        );
    }

    /**
     * Generate yearly rankings
     */
    public function generateYearlyRankings(?int $year = null): array
    {
        $currentYear = $year ?? (int) date('Y');
        $periodValue = (string) $currentYear;

        return $this->generateRankingsForPeriod(
            GameTopRanking::PERIOD_YEAR,
            $periodValue,
            $currentYear - 1
        );
    }

    /**
     * Core method to generate rankings for any period
     */
    private function generateRankingsForPeriod(
        string $periodType,
        string $periodValue,
        int $prevYear,
        ?int $prevPeriod = null
    ): array {
        // Get games data ordered by number of posts (you'll need to adapt this query)
        $gamesData = $this->getGamesPostData($periodType, $periodValue);

        // Get previous period data for comparison
        $prevPeriodValue = $this->formatPreviousPeriodValue($periodType, $prevYear, $prevPeriod);
        $previousRankings = $this->rankingRepository->findBy([
            'periodType' => $periodType,
            'periodValue' => $prevPeriodValue
        ]);

        $previousRanksMap = [];
        foreach ($previousRankings as $prevRanking) {
            $previousRanksMap[$prevRanking->getGame()->getId()] = $prevRanking->getRank();
        }

        $rankings = [];
        $currentRank = 1;

        foreach ($gamesData as $gameData) {
            $game = $gameData['game'];
            $nbPost = $gameData['nbPost'];

            // Calculate position change (positive = improvement, negative = decline)
            $previousRank = $previousRanksMap[$game->getId()] ?? null;
            $positionChange = $previousRank ? $previousRank - $currentRank : null;

            // Check if ranking already exists
            $ranking = $this->rankingRepository->findOneBy([
                'game' => $game,
                'periodType' => $periodType,
                'periodValue' => $periodValue
            ]);

            if (!$ranking) {
                $ranking = new GameTopRanking();
                $ranking->setGame($game);
                $ranking->setPeriodType($periodType);
                $ranking->setPeriodValue($periodValue);
            }

            $ranking->setRank($currentRank);
            $ranking->setNbPost($nbPost);
            $ranking->setPositionChange($positionChange);

            $this->entityManager->persist($ranking);
            $rankings[] = $ranking;
            $currentRank++;
        }

        $this->entityManager->flush();

        return $rankings;
    }

    /**
     * Get games post data for a specific period
     */
    private function getGamesPostData(string $periodType, string $periodValue): array
    {
        [$startDate, $endDate] = $this->getPeriodDateRange($periodType, $periodValue);

        $sql = "
            SELECT dg.id as game_id, SUM(dg.nb_post_day) as total_posts
            FROM dwh_game dg
            WHERE dg.date >= :startDate 
            AND dg.date <= :endDate
            AND dg.nb_post_day > 0
            GROUP BY dg.id
            ORDER BY total_posts DESC
        ";

        $connection = $this->dwhEntityManager->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->bindValue('startDate', $startDate);
        $stmt->bindValue('endDate', $endDate);
        $result = $stmt->executeQuery();

        $gamesData = [];
        $gameRepository = $this->entityManager->getRepository(Game::class);

        while ($row = $result->fetchAssociative()) {
            $game = $gameRepository->find($row['game_id']);
            if ($game) {
                $gamesData[] = [
                    'game' => $game,
                    'nbPost' => (int) $row['total_posts']
                ];
            }
        }

        return $gamesData;
    }

    /**
     * Get date range for a period
     */
    private function getPeriodDateRange(string $periodType, string $periodValue): array
    {
        return match ($periodType) {
            GameTopRanking::PERIOD_WEEK => $this->getWeekDateRange($periodValue),
            GameTopRanking::PERIOD_MONTH => $this->getMonthDateRange($periodValue),
            GameTopRanking::PERIOD_YEAR => $this->getYearDateRange($periodValue),
            default => throw new \InvalidArgumentException("Unknown period type: {$periodType}")
        };
    }

    /**
     * Get date range for a week (format: 2025-W35)
     */
    private function getWeekDateRange(string $periodValue): array
    {
        // Parse "2025-W35" format
        if (!preg_match('/^(\d{4})-W(\d{1,2})$/', $periodValue, $matches)) {
            throw new \InvalidArgumentException("Invalid week format: {$periodValue}");
        }

        $year = (int) $matches[1];
        $week = (int) $matches[2];

        $date = new \DateTime();
        $date->setISODate($year, $week, 1); // Monday of the week
        $startDate = $date->format('Y-m-d');

        $date->setISODate($year, $week, 7); // Sunday of the week
        $endDate = $date->format('Y-m-d');

        return [$startDate, $endDate];
    }

    /**
     * Get date range for a month (format: 2025-08)
     */
    private function getMonthDateRange(string $periodValue): array
    {
        // Parse "2025-08" format
        if (!preg_match('/^(\d{4})-(\d{1,2})$/', $periodValue, $matches)) {
            throw new \InvalidArgumentException("Invalid month format: {$periodValue}");
        }

        $year = (int) $matches[1];
        $month = (int) $matches[2];

        $startDate = sprintf('%d-%02d-01', $year, $month);
        $endDate = (new \DateTime($startDate))->format('Y-m-t'); // Last day of month

        return [$startDate, $endDate];
    }

    /**
     * Get date range for a year (format: 2025)
     */
    private function getYearDateRange(string $periodValue): array
    {
        $year = (int) $periodValue;
        $startDate = "{$year}-01-01";
        $endDate = "{$year}-12-31";

        return [$startDate, $endDate];
    }

    /**
     * Format previous period value
     */
    private function formatPreviousPeriodValue(string $periodType, int $prevYear, ?int $prevPeriod = null): string
    {
        return match ($periodType) {
            GameTopRanking::PERIOD_WEEK => sprintf('%d-W%02d', $prevYear, $prevPeriod),
            GameTopRanking::PERIOD_MONTH => sprintf('%d-%02d', $prevYear, $prevPeriod),
            GameTopRanking::PERIOD_YEAR => (string) $prevYear,
            default => throw new \InvalidArgumentException("Unknown period type: {$periodType}")
        };
    }

    /**
     * Clean old rankings to keep only recent data
     */
    public function cleanOldRankings(int $keepWeeks = 104, int $keepMonths = 24, int $keepYears = 5): void
    {
        $now = new DateTime();

        // Clean old weekly rankings
        $oldWeekDate = clone $now;
        $oldWeekDate->modify("-{$keepWeeks} weeks");
        $oldWeekValue = $oldWeekDate->format('Y-\\WW');
        $this->rankingRepository->deleteOldRankings(GameTopRanking::PERIOD_WEEK, $oldWeekValue);

        // Clean old monthly rankings
        $oldMonthDate = clone $now;
        $oldMonthDate->modify("-{$keepMonths} months");
        $oldMonthValue = $oldMonthDate->format('Y-m');
        $this->rankingRepository->deleteOldRankings(GameTopRanking::PERIOD_MONTH, $oldMonthValue);

        // Clean old yearly rankings
        $oldYear = $now->format('Y') - $keepYears;
        $this->rankingRepository->deleteOldRankings(GameTopRanking::PERIOD_YEAR, (string) $oldYear);
    }
}
