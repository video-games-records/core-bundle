<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\TeamService;

class TeamCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:team';

    private TeamService $teamService;

    public function __construct(EntityManagerInterface $em,TeamService $teamService)
    {
        $this->teamService = $teamService;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:team')
            ->setDescription('Command to update teams ranking')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
        ;
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws DBALException
     * @throws Exception
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj':
                $this->teamService->maj();
                break;
            case 'maj-rank-badge':
                $this->teamService->majRankBadge();
                break;
        }
        return 0;
    }
}
