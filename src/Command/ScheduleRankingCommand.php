<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use VideoGamesRecords\CoreBundle\Scheduler\Message\DailyRanking;

#[AsCommand(
    name: 'vgr:rankings:schedule',
    description: 'Schedule daily ranking generation check'
)]
class ScheduleRankingCommand extends Command
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'Specific date to check (Y-m-d format)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force execution even if not scheduled day')
            ->setHelp('
    This command checks if rankings should be generated today and dispatches the appropriate message.
    
    Automatic schedule:
    - Monday: Generate previous week rankings
    - 1st of month: Generate previous month rankings  
    - 1st of January: Generate previous year rankings + cleanup
    
    Examples:
      # Normal daily execution (at 8:00 AM via cron)
      php bin/console vgr:rankings:schedule
      
      # Test with specific date
      php bin/console vgr:rankings:schedule --date=2025-01-01
      
      # Force execution regardless of date
      php bin/console vgr:rankings:schedule --force
                ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $dateOption = $input->getOption('date');
        $force = $input->getOption('force');

        // Parse date
        $date = new \DateTime();
        if ($dateOption) {
            try {
                $date = new \DateTime($dateOption);
            } catch (\Exception $e) {
                $io->error("Invalid date format: {$dateOption}. Use Y-m-d format.");
                return Command::FAILURE;
            }
        }

        $io->title('VGR Rankings Scheduler');
        $io->info("Checking rankings for date: " . $date->format('Y-m-d l'));

        // Check what rankings should be generated
        $scheduledRankings = $this->getScheduledRankings($date);

        if (empty($scheduledRankings) && !$force) {
            $io->success('No rankings scheduled for today.');
            return Command::SUCCESS;
        }

        if (!empty($scheduledRankings)) {
            $io->section('Scheduled Rankings');
            foreach ($scheduledRankings as $ranking) {
                $io->writeln("• {$ranking}");
            }
        }

        if ($force && empty($scheduledRankings)) {
            $io->warning('Forcing execution even though no rankings are scheduled today.');
        }

        // Dispatch the message
        try {
            $message = new DailyRanking($date);
            $this->messageBus->dispatch($message);

            $io->success('Daily ranking message dispatched successfully!');
            $io->note('Rankings will be processed asynchronously.');
        } catch (\Exception $e) {
            $io->error('Error dispatching message: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Get list of rankings that should be generated for the given date
     */
    private function getScheduledRankings(\DateTime $date): array
    {
        $rankings = [];

        // Check for weekly (Monday)
        if ($date->format('w') === '1') {
            $prevWeek = clone $date;
            $prevWeek->modify('-1 week');
            $rankings[] = sprintf('Weekly rankings for week %s', $prevWeek->format('Y-\\WW'));
        }

        // Check for monthly (1st of month)
        if ($date->format('j') === '1') {
            $prevMonth = clone $date;
            $prevMonth->modify('-1 month');
            $rankings[] = sprintf('Monthly rankings for %s', $prevMonth->format('F Y'));
        }

        // Check for yearly (1st of January)
        if ($date->format('m-d') === '01-01') {
            $prevYear = $date->format('Y') - 1;
            $rankings[] = sprintf('Yearly rankings for %d + cleanup old data', $prevYear);
        }

        return $rankings;
    }
}
