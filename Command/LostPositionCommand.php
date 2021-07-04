<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\LostPositionService;

class LostPositionCommand extends Command
{
    protected static $defaultName = 'vgr-core:lost-position';

    private $lostPositionService;
    private $stack = null;

    public function __construct(LostPositionService $lostPositionService)
    {
        $this->lostPositionService = $lostPositionService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:lost-position')
            ->setDescription('Command to update players ranking')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
             ->addOption(
                'debug',
                'd',
                InputOption::VALUE_NONE,
                'Debug option (sql)'
            );
        ;
    }

    /**
     * @param InputInterface $input
     */
    private function init(InputInterface $input)
    {
        if ($input->getOption('debug')) {
            // Start setup logger
            $doctrineConnection = $this->playerService->getEntityManager()->getConnection();
            $this->stack = new DebugStack();
            $doctrineConnection->getConfiguration()->setSQLLogger($this->stack);
            // End setup logger
        }
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
            case 'purge':
                $this->lostPositionService->purge();
                break;
        }
        return 0;
    }
}
