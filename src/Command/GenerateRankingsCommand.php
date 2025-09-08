<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use VideoGamesRecords\CoreBundle\Service\GameRankingService;
use VideoGamesRecords\CoreBundle\Service\PlayerRankingService;

#[AsCommand(
    name: 'vgr:rankings:generate',
    description: 'Generate rankings for specified type and period'
)]
class GenerateRankingsCommand extends Command
{
    public function __construct(
        private GameRankingService $gameRankingService,
        private PlayerRankingService $playerRankingService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('type', InputArgument::REQUIRED, 'Ranking type (game, player)')
            ->addArgument('period', InputArgument::REQUIRED, 'Period type (week, month, year)')
            ->addOption('year', 'y', InputOption::VALUE_OPTIONAL, 'Specific year')
            ->addOption('month', 'm', InputOption::VALUE_OPTIONAL, 'Specific month (1-12)')
            ->addOption('week', 'w', InputOption::VALUE_OPTIONAL, 'Specific week (1-53)')
            ->addOption('clean', 'c', InputOption::VALUE_NONE, 'Clean old rankings after generation')
            ->setHelp('
This command generates rankings for the specified type and period.

Examples:
  # Generate current week game rankings
  php bin/console vgr:rankings:generate game week
  
  # Generate game rankings for specific week
  php bin/console vgr:rankings:generate game week --year=2025 --week=35
  
  # Generate current month player rankings
  php bin/console vgr:rankings:generate player month
  
  # Generate player rankings for specific month
  php bin/console vgr:rankings:generate player month --year=2025 --month=8
  
  # Generate current year game rankings and clean old data
  php bin/console vgr:rankings:generate game year --clean
            ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $type = $input->getArgument('type');
        $period = $input->getArgument('period');
        $year = $input->getOption('year') ? (int) $input->getOption('year') : null;
        $month = $input->getOption('month') ? (int) $input->getOption('month') : null;
        $week = $input->getOption('week') ? (int) $input->getOption('week') : null;
        $clean = $input->getOption('clean');

        $io->title('Video Games Records - Rankings Generator');

        // Validate type
        if (!in_array($type, ['game', 'player'])) {
            $io->error("Invalid type '{$type}'. Use: game or player");
            return Command::FAILURE;
        }

        // Validate period
        if (!in_array($period, ['week', 'month', 'year'])) {
            $io->error("Invalid period '{$period}'. Use: week, month, or year");
            return Command::FAILURE;
        }

        try {
            $rankings = [];

            if ($type === 'game') {
                $rankings = $this->generateGameRankings($io, $period, $year, $month, $week);
            } else {
                $rankings = $this->generatePlayerRankings($io, $period, $year, $month, $week);
            }

            $periodInfo = $this->getPeriodInfo($period, $year, $month, $week);
            $io->success("Generated " . count($rankings) . " {$type} rankings for {$periodInfo}");

            // Display top 10 rankings
            if (!empty($rankings)) {
                $this->displayTopRankings($io, $rankings, $type);
            }

            // Clean old rankings if requested
            if ($clean) {
                $io->section('Cleaning Old Rankings');
                if ($type === 'game') {
                    $this->gameRankingService->cleanOldRankings();
                } else {
                    $this->playerRankingService->cleanOldRankings();
                }
                $io->success('Old rankings cleaned successfully');
            }
        } catch (\Exception $e) {
            $io->error('Error generating rankings: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $io->success('Rankings generation completed successfully!');
        return Command::SUCCESS;
    }

    private function generateGameRankings(SymfonyStyle $io, string $period, ?int $year, ?int $month, ?int $week): array
    {
        switch ($period) {
            case 'week':
                $io->section('Generating Weekly Game Rankings');
                if ($year && !$week) {
                    throw new \InvalidArgumentException('Week number is required when year is specified for weekly rankings');
                }
                return $this->gameRankingService->generateWeeklyRankings($year, $week);

            case 'month':
                $io->section('Generating Monthly Game Rankings');
                if ($year && !$month) {
                    throw new \InvalidArgumentException('Month number is required when year is specified for monthly rankings');
                }
                return $this->gameRankingService->generateMonthlyRankings($year, $month);

            case 'year':
                $io->section('Generating Yearly Game Rankings');
                return $this->gameRankingService->generateYearlyRankings($year);

            default:
                throw new \InvalidArgumentException("Invalid period '{$period}'");
        }
    }

    private function generatePlayerRankings(SymfonyStyle $io, string $period, ?int $year, ?int $month, ?int $week): array
    {
        switch ($period) {
            case 'week':
                $io->section('Generating Weekly Player Rankings');
                if ($year && !$week) {
                    throw new \InvalidArgumentException('Week number is required when year is specified for weekly rankings');
                }
                return $this->playerRankingService->generateWeeklyRankings($year, $week);

            case 'month':
                $io->section('Generating Monthly Player Rankings');
                if ($year && !$month) {
                    throw new \InvalidArgumentException('Month number is required when year is specified for monthly rankings');
                }
                return $this->playerRankingService->generateMonthlyRankings($year, $month);

            case 'year':
                $io->section('Generating Yearly Player Rankings');
                return $this->playerRankingService->generateYearlyRankings($year);

            default:
                throw new \InvalidArgumentException("Invalid period '{$period}'");
        }
    }

    private function getPeriodInfo(string $period, ?int $year, ?int $month, ?int $week): string
    {
        return match ($period) {
            'week' => $year && $week ? "Week {$week}/{$year}" : 'Current Week',
            'month' => $year && $month ? "Month {$month}/{$year}" : 'Current Month',
            'year' => $year ? "Year {$year}" : 'Current Year',
            default => 'Unknown Period'
        };
    }

    private function displayTopRankings(SymfonyStyle $io, array $rankings, string $type): void
    {
        $io->section("Top 10 {$type} Rankings");
        $tableData = [];

        foreach (array_slice($rankings, 0, 10) as $ranking) {
            $positionChange = $ranking->getPositionChange();
            $changeSymbol = match (true) {
                $positionChange === null => '🆕',
                $positionChange > 0 => '📈 +' . $positionChange,
                $positionChange < 0 => '📉 ' . $positionChange,
                default => '➡️ 0'
            };

            if ($type === 'game') {
                $name = $ranking->getGame()->getName() ?? 'N/A';
                $metric = $ranking->getNbPost();
                $metricLabel = 'Posts';
            } else {
                $name = $ranking->getPlayer()->getPseudo() ?? 'N/A';
                $metric = $ranking->getNbPost();
                $metricLabel = 'Posts';
            }

            $tableData[] = [
                $ranking->getRank(),
                $name,
                $metric,
                $changeSymbol
            ];
        }

        $io->table(
            ['Rank', ucfirst($type), $metricLabel, 'Change'],
            $tableData
        );
    }
}
