<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\Ranking\TeamGameRanking;
use Symfony\Component\Console\Command\Command;

class MajTeamGameRankingCommand extends Command
{
    protected static $defaultName = 'vgr-core:game-ranking';

    private TeamGameRanking $teamGameRanking;

    public function __construct(TeamGameRanking $teamGameRanking)
    {
        $this->teamGameRanking = $teamGameRanking;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:maj-team-game-ranking')
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
        $this->teamGameRanking->maj($input->getOption('id'));
        return 0;
    }
}
