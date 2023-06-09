<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Handler\ScoreInvestigationHandler;

class ScoreInvestigationUpdate extends Command
{
    protected static $defaultName = 'vgr-core:score-investigation-update';

    private ScoreInvestigationHandler $scoreInvestigationHandler;

    public function __construct(ScoreInvestigationHandler $scoreInvestigationHandler)
    {
        $this->scoreInvestigationHandler = $scoreInvestigationHandler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('vgr-core:score-investigation-update')
            ->setDescription('Command to check score under investigation');
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
        $this->scoreInvestigationHandler->process();
        return Command::SUCCESS;
    }
}
