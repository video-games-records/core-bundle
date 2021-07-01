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
use VideoGamesRecords\CoreBundle\Service\CountryService;

class CountryCommand extends Command
{
    protected static $defaultName = 'vgr-core:country';

    private $countryService;
    private $stack = null;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:player')
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
     * @throws Exception
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input);
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj':
                $this->countryService->maj();
                break;
        }
        if ($this->stack != null) {
            $output->writeln(sprintf('%s queries', count($this->stack->queries)));
        }
        return 0;
    }
}
