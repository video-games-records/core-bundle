<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Service\PlatformService;

class PlayerPlatformCommand extends Command
{
    protected static $defaultName = 'vgr-core:player-platform';

    private PlatformService $platformService;

    public function __construct(PlatformService $platformService)
    {
        $this->platformService = $platformService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:player-platform')
            ->setDescription('Command to update platform rankings for players')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'idPlatform',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
        ;
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
            case 'maj-all':
                $this->platformService->majAll();
                break;
            case 'maj':
                $this->platformService->majRanking($input->getOption('idPlatform'));
                break;
        }
        return 0;
    }
}
