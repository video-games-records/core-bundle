<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Scheduler\Handler;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use VideoGamesRecords\CoreBundle\Scheduler\Message\DailyRanking;
use VideoGamesRecords\CoreBundle\Service\GameRankingService;
use VideoGamesRecords\CoreBundle\Service\PlayerRankingService;

#[AsMessageHandler]
readonly class DailyRankingHandler
{
    public function __construct(
        private GameRankingService $gameRankingService,
        private PlayerRankingService $playerRankingService,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(DailyRanking $message): void
    {
        $date = $message->getDate() ?? new \DateTime();
        $this->logger->info('Daily ranking check started', ['date' => $date->format('Y-m-d')]);

        try {
            $rankingsGenerated = [];

            // Check if we need to generate weekly rankings (every Monday)
            if ($this->shouldGenerateWeeklyRankings($date)) {
                $rankingsGenerated['weekly_games'] = $this->generateWeeklyRankings($date);
                $rankingsGenerated['weekly_players'] = $this->generateWeeklyPlayerRankings($date);
            }

            // Check if we need to generate monthly rankings (1st of month)
            if ($this->shouldGenerateMonthlyRankings($date)) {
                $rankingsGenerated['monthly_games'] = $this->generateMonthlyRankings($date);
                $rankingsGenerated['monthly_players'] = $this->generateMonthlyPlayerRankings($date);
            }

            // Check if we need to generate yearly rankings (1st of January)
            if ($this->shouldGenerateYearlyRankings($date)) {
                $rankingsGenerated['yearly_games'] = $this->generateYearlyRankings($date);
                $rankingsGenerated['yearly_players'] = $this->generateYearlyPlayerRankings($date);
            }

            // Log results
            if (empty($rankingsGenerated)) {
                $this->logger->info('No rankings to generate today');
            } else {
                foreach ($rankingsGenerated as $type => $count) {
                    $this->logger->info("Generated {$type} rankings", ['count' => $count]);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Error during daily ranking generation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Check if we should generate weekly rankings (every Monday)
     */
    private function shouldGenerateWeeklyRankings(\DateTime $date): bool
    {
        return $date->format('w') === '1'; // Monday = 1
    }

    /**
     * Check if we should generate monthly rankings (1st of month)
     */
    private function shouldGenerateMonthlyRankings(\DateTime $date): bool
    {
        return $date->format('j') === '1'; // 1st day of month
    }

    /**
     * Check if we should generate yearly rankings (1st of January)
     */
    private function shouldGenerateYearlyRankings(\DateTime $date): bool
    {
        return $date->format('m-d') === '01-01'; // January 1st
    }

    /**
     * Generate weekly rankings for the previous week
     */
    private function generateWeeklyRankings(\DateTime $date): int
    {
        // Get previous week
        $previousWeek = clone $date;
        $previousWeek->modify('-1 week');

        $year = (int) $previousWeek->format('Y');
        $week = (int) $previousWeek->format('W');

        $this->logger->info("Generating weekly rankings", [
            'year' => $year,
            'week' => $week,
            'period' => sprintf('%d-W%02d', $year, $week)
        ]);

        $rankings = $this->gameRankingService->generateWeeklyRankings($year, $week);

        return count($rankings);
    }

    /**
     * Generate monthly rankings for the previous month
     */
    private function generateMonthlyRankings(\DateTime $date): int
    {
        // Get previous month
        $previousMonth = clone $date;
        $previousMonth->modify('-1 month');

        $year = (int) $previousMonth->format('Y');
        $month = (int) $previousMonth->format('n');

        $this->logger->info("Generating monthly rankings", [
            'year' => $year,
            'month' => $month,
            'period' => sprintf('%d-%02d', $year, $month)
        ]);

        $rankings = $this->gameRankingService->generateMonthlyRankings($year, $month);

        return count($rankings);
    }

    /**
     * Generate yearly rankings for the previous year
     */
    private function generateYearlyRankings(\DateTime $date): int
    {
        // Get previous year
        $previousYear = (int) $date->format('Y') - 1;

        $this->logger->info("Generating yearly rankings", [
            'year' => $previousYear,
            'period' => (string) $previousYear
        ]);

        $rankings = $this->gameRankingService->generateYearlyRankings($previousYear);

        // Clean old rankings when generating yearly rankings
        //$this->gameRankingService->cleanOldRankings();
        //$this->logger->info("Old game rankings cleaned");

        return count($rankings);
    }

    /**
     * Generate weekly player rankings for the previous week
     */
    private function generateWeeklyPlayerRankings(\DateTime $date): int
    {
        // Get previous week
        $previousWeek = clone $date;
        $previousWeek->modify('-1 week');

        $year = (int) $previousWeek->format('Y');
        $week = (int) $previousWeek->format('W');

        $this->logger->info("Generating weekly player rankings", [
            'year' => $year,
            'week' => $week,
            'period' => sprintf('%d-W%02d', $year, $week)
        ]);

        $rankings = $this->playerRankingService->generateWeeklyRankings($year, $week);

        return count($rankings);
    }

    /**
     * Generate monthly player rankings for the previous month
     */
    private function generateMonthlyPlayerRankings(\DateTime $date): int
    {
        // Get previous month
        $previousMonth = clone $date;
        $previousMonth->modify('-1 month');

        $year = (int) $previousMonth->format('Y');
        $month = (int) $previousMonth->format('n');

        $this->logger->info("Generating monthly player rankings", [
            'year' => $year,
            'month' => $month,
            'period' => sprintf('%d-%02d', $year, $month)
        ]);

        $rankings = $this->playerRankingService->generateMonthlyRankings($year, $month);

        return count($rankings);
    }

    /**
     * Generate yearly player rankings for the previous year
     */
    private function generateYearlyPlayerRankings(\DateTime $date): int
    {
        // Get previous year
        $previousYear = (int) $date->format('Y') - 1;

        $this->logger->info("Generating yearly player rankings", [
            'year' => $previousYear,
            'period' => (string) $previousYear
        ]);

        $rankings = $this->playerRankingService->generateYearlyRankings($previousYear);

        // Clean old player rankings when generating yearly rankings
        //$this->playerRankingService->cleanOldRankings();
        //$this->logger->info("Old player rankings cleaned");

        return count($rankings);
    }
}
