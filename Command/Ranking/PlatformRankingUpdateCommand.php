<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\Ranking\Updater\PlatformRankingUpdater;

class PlatformRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:platform-ranking-update';

    private PlatformRankingUpdater $platformRankingUpdater;

    public function __construct(PlatformRankingUpdater $platformRankingUpdater)
    {
        $this->platformRankingUpdater = $platformRankingUpdater;
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
                $this->platformRankingUpdater->maj($id);
                break;
            case 'maj-all':
                $this->platformRankingUpdater->majAll();
                break;
        }
        return 0;
    }
}
