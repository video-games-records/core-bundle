<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Service\DataService;

class DataCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:data';

    private EntityManagerInterface $em;
    private DataService $dataService;

    public function __construct(EntityManagerInterface $em, DataService $dataService)
    {
        $this->em = $em;
        $this->dataService = $dataService;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:data')
            ->setDescription('Command to update data')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'What do you want to do?'
            )
            ->addOption(
                'debug',
                null,
                InputOption::VALUE_NONE,
                ''
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input);
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj-user-record-connexion':
                $this->dataService->majUserRecordConnexion();
                break;
        }
        $this->end($output);

        return 0;
    }
}
