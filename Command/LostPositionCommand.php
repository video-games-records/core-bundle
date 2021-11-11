<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\LostPositionService;

class LostPositionCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:lost-position';

    private EntityManagerInterface $em;
    private LostPositionService $lostPositionService;

    public function __construct(EntityManagerInterface $em, LostPositionService $lostPositionService)
    {
        $this->lostPositionService = $lostPositionService;
        parent::__construct($em);
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
        $function = $input->getArgument('function');
        switch ($function) {
            case 'purge':
                $this->lostPositionService->purge();
                break;
        }
        return 0;
    }
}
