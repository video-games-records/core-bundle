<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\Ranking\PlayerGameRanking;
use Symfony\Component\Console\Command\Command;

class MajPlayerGameRankingCommand extends Command
{
    protected static $defaultName = 'vgr-core:maj-player-game-ranking';

    private PlayerGameRanking $playerGameRanking;

    public function __construct(PlayerGameRanking $playerGameRanking)
    {
        $this->playerGameRanking = $playerGameRanking;
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
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->playerGameRanking->maj($input->getOption('id'));
        return 0;
    }
}
