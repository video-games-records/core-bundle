<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Handler\ScoreInvestigationHandler;

#[AsCommand(
    name: 'vgr-core:score-investigation-update',
    description: 'Command to check score under investigation'
)]
class ScoreInvestigationUpdate extends Command
{
    private ScoreInvestigationHandler $scoreInvestigationHandler;

    public function __construct(ScoreInvestigationHandler $scoreInvestigationHandler)
    {
        $this->scoreInvestigationHandler = $scoreInvestigationHandler;
        parent::__construct();
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws OptimisticLockException|ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->scoreInvestigationHandler->handle();
        return Command::SUCCESS;
    }
}
