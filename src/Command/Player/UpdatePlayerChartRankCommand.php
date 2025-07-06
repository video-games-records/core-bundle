<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Command\Player;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerChartRank;

#[AsCommand(
    name: 'vgr-core:player-chart-rank-update',
    description: 'Command to update player charts ranking'
)]
class UpdatePlayerChartRankCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $bus
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'id-chart',
                null,
                InputOption::VALUE_REQUIRED
            )
        ;
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $idChart = $input->getOption('id-chart');
        if (null === $idChart) {
            echo "Option id-chart is required\n";
            return Command::INVALID;
        }

        $this->bus->dispatch(new UpdatePlayerChartRank((int) $idChart));

        return Command::SUCCESS;
    }
}
