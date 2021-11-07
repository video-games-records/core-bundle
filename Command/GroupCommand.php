<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Service\GroupService;

class GroupCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:group';

    private EntityManagerInterface $em;
    private GroupService $groupService;

    public function __construct(EntityManagerInterface $em, GroupService $groupService)
    {
        $this->em = $em;
        $this->groupService = $groupService;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:group')
            ->setDescription('Command to update group rankings for players')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'idGroup',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
        ;
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
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj-player':
                $idGroup = $input->getOption('idGroup');
                $this->groupService->majPlayerGroup($idGroup);
                break;
            case 'maj-team':
                $idGroup = $input->getOption('idGroup');
                $this->groupService->majTeamGroup($idGroup);
                break;
        }
        return 1;
    }
}
