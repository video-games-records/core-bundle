<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\Ranking\PlayerPlatformRankingUpdate;

class PlayerPlatformRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:player-platform-ranking-update';

    private PlayerPlatformRankingUpdate $playerPlatformRankingUpdate;

    public function __construct(PlayerPlatformRankingUpdate $playerPlatformRankingUpdate)
    {
        $this->playerPlatformRankingUpdate = $playerPlatformRankingUpdate;
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
                $this->playerPlatformRankingUpdate->maj($id);
                break;
            case 'maj-all':
                $this->playerPlatformRankingUpdate->majAll();
                break;
        }
        return 0;
    }
}
