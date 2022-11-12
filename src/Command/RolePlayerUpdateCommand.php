<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\RolePlayerManager;

class RolePlayerUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:role-player-update';

    private RolePlayerManager $rolePlayerManager;

    public function __construct(RolePlayerManager $rolePlayerManager)
    {
        $this->rolePlayerManager = $rolePlayerManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:role-player-update')
            ->setDescription('Command to add/remove player role')
        ;
        parent::configure();
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws OptimisticLockException|ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->rolePlayerManager->majRulesOfThree();
        return 0;
    }
}
