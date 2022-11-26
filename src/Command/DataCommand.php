<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\DataService;

class DataCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:data';

    private EntityManagerInterface $em;
    private DataService $dataService;

    public function __construct(EntityManagerInterface $em, DataService $dataService)
    {
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
        ;
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input);
        $function = $input->getArgument('function');
        if ($function == 'maj-user-record-connexion') {
            $this->dataService->majUserRecordConnexion();
        }
        $this->end($output);

        return 0;
    }
}
