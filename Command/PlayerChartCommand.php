<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\PlayerChart as Service;

class PlayerChartCommand extends Command
{
    protected static $defaultName = 'vgr-core:player-chart';

    private $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:player-chart')
            ->setDescription('Command to maj master player-chart data')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'What do you want to do?'
            )
            ->addOption(
                'debug',
                null,
                InputOption::VALUE_NONE,
                'Debug option (sql)'
            )
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return bool
     * @throws ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output): bool
    {
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj-investigation':
                $this->service->majInvestigation();
                break;
        }
        return true;
    }
}
