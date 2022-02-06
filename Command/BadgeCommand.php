<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\DBAL\DBALException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Service\BadgeService;

class BadgeCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:badge';

    private EntityManagerInterface $em;
    private BadgeService $badgeService;

    public function __construct(EntityManagerInterface $em, BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:badge')
            ->setDescription('Command to maj player badges')
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
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input);
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj':
                $this->badgeService->majUserBadge();
                $this->badgeService->majPlayerBadge();
                break;
        }
        $this->end($output);
        return 0;
    }
}
