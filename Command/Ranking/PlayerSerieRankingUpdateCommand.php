<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\Ranking\Updater\PlayerSerieRankingUpdater;

class PlayerSerieRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:player-serie-ranking-update';

    private PlayerSerieRankingUpdater $playerSerieRankingUpdater;

    public function __construct(PlayerSerieRankingUpdater $playerSerieRankingUpdater)
    {
        $this->playerSerieRankingUpdater = $playerSerieRankingUpdater;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:player-platform-ranking-update')
            ->setDescription('Command to update players ranking')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
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
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj':
                $id = $input->getOption('id');
                $this->playerSerieRankingUpdater->maj($id);
                break;
            case 'maj-all':
                $this->playerSerieRankingUpdater->majAll();
                break;
        }
        return 0;
    }
}
