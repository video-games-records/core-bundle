<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\Badge\PlayerBadgeUpdater;

class PlayerBadgeUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:player-badge-update';

    private PlayerBadgeUpdater $playerBadgeUpdater;

    public function __construct(PlayerBadgeUpdater $playerBadgeUpdater)
    {
        $this->playerBadgeUpdater = $playerBadgeUpdater;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:player-badge-update')
            ->setDescription('Command to maj player badges')
        ;
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->playerBadgeUpdater->process();
        return 0;
    }
}
