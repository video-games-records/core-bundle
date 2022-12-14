<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\Ranking\Write\PlayerGameRankingHandler;

class PlayerGameRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:player-game-ranking-update';

    private PlayerGameRankingHandler $playerGameRankingHandler;

    public function __construct(PlayerGameRankingHandler $playerGameRankingHandler)
    {
        $this->playerGameRankingHandler = $playerGameRankingHandler;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:maj-player-game-ranking')
            ->setDescription('Command to update game rankings for players')
            ->addOption(
                'id',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
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
        $this->playerGameRankingHandler->handle($input->getOption('id'));
        return Command::SUCCESS;
    }
}
