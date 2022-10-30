<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\Ranking\Updater\TeamGameRankingUpdater;

class TeamGameRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:team-game-ranking-update';

    private TeamGameRankingUpdater $teamGameRankingUpdater;

    public function __construct(TeamGameRankingUpdater $teamGameRankingUpdater)
    {
        $this->teamGameRankingUpdater = $teamGameRankingUpdater;
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
        $this->teamGameRankingUpdater->maj($input->getOption('id'));
        return 0;
    }
}
