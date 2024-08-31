<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Command;

use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Manager\GameOfDayManager;

#[AsCommand(
    name: 'vgr-core:game-of-day-add',
    description: 'Command to add game of day'
)]
class GameOfDayAddCommand extends Command
{
    private GameOfDayManager $gameOfDayManager;

    public function __construct(GameOfDayManager $gameOfDayManager)
    {
        $this->gameOfDayManager = $gameOfDayManager;
        parent::__construct();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->gameOfDayManager->addTomorrowGame();
        return Command::SUCCESS;
    }
}
