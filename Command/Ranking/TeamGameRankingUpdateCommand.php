<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\Ranking\TeamGameRankingUpdate;
use Symfony\Component\Console\Command\Command;

class TeamGameRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:team-game-ranking-update';

    private TeamGameRankingUpdate $teamGameRankingUpdate;

    public function __construct(TeamGameRankingUpdate $teamGameRankingUpdate)
    {
        $this->teamGameRankingUpdate = $teamGameRankingUpdate;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:team-game-ranking-update')
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
        $this->teamGameRankingUpdate->maj($input->getOption('id'));
        return 0;
    }
}
