<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Service\PlayerChartService;

class PlayerChartCommand extends Command
{
    protected static $defaultName = 'vgr-core:player-chart';

    private $playerChartService;
    private $nbChartToMaj = 100;
    private $stack = null;

    public function __construct(PlayerChartService $playerChartService)
    {
        $this->playerChartService = $playerChartService;
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
                'nbChartToMaj',
                null,
                InputOption::VALUE_OPTIONAL,
                'Nb Chart to MAJ'
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
     * @param InputInterface $input
     */
    private function init(InputInterface $input)
    {
        if ($input->getOption('debug')) {
            // Start setup logger
            $doctrineConnection = $this->playerChartService->getEntityManager()->getConnection();
            $this->stack = new DebugStack();
            $doctrineConnection->getConfiguration()->setSQLLogger($this->stack);
            // End setup logger
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws DBALException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input);
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj-investigation':
                $this->playerChartService->majInvestigation();
                break;
            case 'maj-ranking':
                if ($input->getOption('nbChartToMaj')) {
                    $this->nbChartToMaj = $input->getOption('nbChartToMaj');
                }
                $this->playerChartService->majRanking();
                break;
        }
        if ($this->stack != null) {
            $output->writeln(sprintf('%s queries', count($this->stack->queries)));
        }
        return 0;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException|DBALException
     */
    private function majRanking($output)
    {
        if ($this->playerChartService->isMajRunning()) {
            $output->writeln('vgr:chart maj-player is already running');
            return;
        }
        $nb = $this->playerChartService->majRanking($this->nbChartToMaj);
        $output->writeln(sprintf('%d chart(s) updated', $nb));
    }
}
