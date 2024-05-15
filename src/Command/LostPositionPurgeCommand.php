<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Manager\LostPositionManager;
use VideoGamesRecords\CoreBundle\Repository\LostPositionRepository;

class LostPositionPurgeCommand extends Command
{
    protected static $defaultName = 'vgr-core:lost-position-purge';

    private LostPositionManager $lostPositionManager;

    public function __construct(LostPositionManager $lostPositionManager)
    {
        $this->lostPositionManager = $lostPositionManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('vgr-core:lost-position-purge')
            ->setDescription('Purge data')
        ;
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->lostPositionManager->purge();
        return Command::SUCCESS;
    }
}
