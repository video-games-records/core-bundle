<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Handler\Badge\PlayerBadgeHandler;

#[AsCommand(
    name: 'vgr-core:player-badge-update',
    description: 'Command to maj player badges'
)]
class PlayerBadgeUpdateCommand extends Command
{
    private PlayerBadgeHandler $playerBadgeHandler;

    public function __construct(PlayerBadgeHandler $playerBadgeHandler)
    {
        $this->playerBadgeHandler = $playerBadgeHandler;
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
        $this->playerBadgeHandler->handle();
        return Command::SUCCESS;
    }
}
