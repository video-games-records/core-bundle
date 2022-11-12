<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\PlayerChartService;

class ScoreInvestigationUpdate extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:score-investigation-update';

    private PlayerChartService $playerChartService;

    public function __construct(
        EntityManagerInterface $em,
        PlayerChartService $playerChartService
    ) {
        $this->playerChartService = $playerChartService;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:score-investigation-update')
            ->setDescription('Command to check score under investigation')
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
     * @throws OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input);
        $function = $input->getArgument('function');
        if ($function == 'maj-investigation') {
            $this->playerChartService->majInvestigation();
        }
        $this->end($output);
        return 0;
    }
}
