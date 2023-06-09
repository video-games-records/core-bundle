<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\Ranking\Write\PlayerRankingHandler;

class PlayerRankingBadgeUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:player-ranking-badge-update';

    private PlayerRankingHandler $playerRankingHandler;

    public function __construct(PlayerRankingHandler $playerRankingHandler)
    {
        $this->playerRankingHandler = $playerRankingHandler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('vgr-core:player-ranking-badge-update')
            ->setDescription('Command to update players ranking')
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
        $this->playerRankingHandler->majRankBadge();
        return Command::SUCCESS;
    }
}
