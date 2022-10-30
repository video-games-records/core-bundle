<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\PlayerService;

class PlayerCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:player';

    private PlayerService $playerService;

    public function __construct(EntityManagerInterface $em,PlayerService $playerService)
    {
        $this->playerService = $playerService;
        parent::__construct($em);
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
        ;
        parent::configure();
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
                //$this->playerService->maj();
                break;
            case 'maj-rank-badge':
                $this->playerService->majRankBadge();
                break;
            case 'maj-role-player':
                $this->playerService->majRulesOfThree();
                break;
        }
        $this->end($output);
        return 0;
    }
}
