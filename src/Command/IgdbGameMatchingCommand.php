<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Domain\Igdb\Service\GameMatchingService;

#[AsCommand(
    name: 'vgr:igdb:match-games',
    description: 'Automatically match Core Bundle games with IGDB games'
)]
class IgdbGameMatchingCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly GameMatchingService $gameMatchingService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'dry-run',
                'd',
                InputOption::VALUE_NONE,
                'Show what would be matched without actually updating the database'
            )
            ->addOption(
                'game-id',
                'g',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Process only specific game IDs (can be used multiple times)'
            )
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Maximum number of games to process',
                100
            )
            ->addOption(
                'batch-size',
                'b',
                InputOption::VALUE_OPTIONAL,
                'Number of games to process in each batch before flushing',
                10
            )
            ->addOption(
                'without-igdb-only',
                'w',
                InputOption::VALUE_NONE,
                'Process only games that do not have an IGDB association yet'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $gameIds = $input->getOption('game-id');
        $limit = (int) $input->getOption('limit');
        $batchSize = (int) $input->getOption('batch-size');
        $withoutIgdbOnly = $input->getOption('without-igdb-only');

        $io->title('IGDB Game Matching Service');

        if ($dryRun) {
            $io->note('Running in DRY-RUN mode - no changes will be made to the database');
        }

        try {
            $games = $this->getGamesToProcess($gameIds, $withoutIgdbOnly, $limit);
            $totalGames = count($games);

            if ($totalGames === 0) {
                $io->success('No games to process.');
                return Command::SUCCESS;
            }

            $io->info(sprintf('Found %d games to process', $totalGames));

            if (!$dryRun) {
                $statistics = $this->gameMatchingService->getMatchingStatistics();
                $io->section('Current Statistics');
                $io->table(
                    ['Metric', 'Value'],
                    [
                        ['Total Games', $statistics['total_games']],
                        ['Games with IGDB Association', $statistics['games_with_igdb_association']],
                        ['Games without IGDB Association', $statistics['games_without_igdb_association']],
                        ['Matching Percentage', $statistics['matching_percentage'] . '%']
                    ]
                );
            }

            $io->progressStart($totalGames);

            $processedCount = 0;
            $matchedCount = 0;
            $errorCount = 0;
            $skippedCount = 0;

            foreach (array_chunk($games, $batchSize) as $gameBatch) {
                foreach ($gameBatch as $game) {
                    try {
                        if ($game->getIgdbGame() !== null && $withoutIgdbOnly) {
                            $skippedCount++;
                            $io->progressAdvance();
                            continue;
                        }

                        if ($dryRun) {
                            $io->text(sprintf(
                                'Would process: %s (ID: %d) - Platforms: %s',
                                $game->getLibGameEn(),
                                $game->getId(),
                                $this->getPlatformNames($game)
                            ));
                            $processedCount++;
                        } else {
                            $igdbGame = $this->gameMatchingService->findAndAssociateIgdbGame($game);

                            if ($igdbGame !== null) {
                                $matchedCount++;
                                $io->text(sprintf(
                                    'Matched: %s → IGDB Game ID: %d',
                                    $game->getLibGameEn(),
                                    $igdbGame->getId()
                                ));
                            }
                            $processedCount++;
                        }
                    } catch (\Exception $e) {
                        $errorCount++;
                        $io->error(sprintf(
                            'Error processing game %s (ID: %d): %s',
                            $game->getLibGameEn(),
                            $game->getId(),
                            $e->getMessage()
                        ));
                    }

                    $io->progressAdvance();
                }

                if (!$dryRun) {
                    $this->entityManager->clear();
                }
            }

            $io->progressFinish();

            $io->section('Processing Summary');
            $resultTable = [
                ['Total Games Processed', $processedCount],
                ['Successfully Matched', $matchedCount],
                ['Errors', $errorCount],
                ['Skipped (already matched)', $skippedCount]
            ];

            if ($dryRun) {
                $resultTable[] = ['⚠️  DRY RUN MODE', 'No changes made'];
            }

            $io->table(['Metric', 'Count'], $resultTable);

            if (!$dryRun && $matchedCount > 0) {
                $io->section('Updated Statistics');
                $finalStatistics = $this->gameMatchingService->getMatchingStatistics();
                $io->table(
                    ['Metric', 'Value'],
                    [
                        ['Total Games', $finalStatistics['total_games']],
                        ['Games with IGDB Association', $finalStatistics['games_with_igdb_association']],
                        ['Games without IGDB Association', $finalStatistics['games_without_igdb_association']],
                        ['Matching Percentage', $finalStatistics['matching_percentage'] . '%']
                    ]
                );
            }

            if ($errorCount > 0) {
                $io->warning(sprintf('%d errors occurred during processing. Check the logs for details.', $errorCount));
                return Command::FAILURE;
            }

            $io->success('Game matching completed successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Command failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function getGamesToProcess(?array $gameIds, bool $withoutIgdbOnly, int $limit): array
    {
        $queryBuilder = $this->entityManager->getRepository(Game::class)->createQueryBuilder('g');

        if (!empty($gameIds)) {
            $queryBuilder->where('g.id IN (:gameIds)')
                        ->setParameter('gameIds', array_map('intval', $gameIds));
        }

        if ($withoutIgdbOnly) {
            $queryBuilder->andWhere('g.igdbGame IS NULL');
        }

        $queryBuilder->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }

    private function getPlatformNames(Game $game): string
    {
        $platformNames = [];
        foreach ($game->getPlatforms() as $platform) {
            $platformNames[] = $platform->getLibPlatform();
        }
        return implode(', ', $platformNames);
    }
}
