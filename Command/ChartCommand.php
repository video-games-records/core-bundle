<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Service\ChartService;
use VideoGamesRecords\CoreBundle\Service\PlayerChartService;
use VideoGamesRecords\CoreBundle\Service\TeamChartService;

class ChartCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:chart';

    private ChartService $chartService;
    private PlayerChartService $playerChartService;
    private TeamChartService $teamChartService;
    private int $nbChartToMaj = 100;

    public function __construct(
        EntityManagerInterface $em,
        ChartService $chartService,
        PlayerChartService $playerChartService,
        TeamChartService $teamChartService
    )
    {
        $this->chartService = $chartService;
        $this->playerChartService = $playerChartService;
        $this->teamChartService = $teamChartService;
        parent::__construct($em);
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
        ;
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
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
            case 'maj-player':
                if ($input->getOption('nbChartToMaj')) {
                    $this->nbChartToMaj = $input->getOption('nbChartToMaj');
                }
                $this->majPlayer($output);
                break;
            case 'maj-team':
                if ($input->getOption('nbChartToMaj')) {
                    $this->nbChartToMaj = $input->getOption('nbChartToMaj');
                }
                $this->majTeam($output);
                break;

        }
        $this->end($output);
        return 0;
    }

    /**
     * @param $output
     * @throws ExceptionInterface
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function majPlayer($output)
    {
        if ($this->chartService->isMajPlayerRunning()) {
            $output->writeln('vgr:chart maj-player is already running');
            return;
        }
        $nb = $this->playerChartService->majRanking($this->nbChartToMaj);
        $output->writeln(sprintf('%d chart(s) updated', $nb));
    }

    /**
     * @param $output
     * @throws ExceptionInterface
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function majTeam($output)
    {
        if ($this->chartService->isMajTeamRunning()) {
            $output->writeln('vgr:chart maj-team is already running');
            return;
        }
        $nb = $this->teamChartService->majRanking($this->nbChartToMaj);
        $output->writeln(sprintf('%d chart(s) updated', $nb));
    }
}
