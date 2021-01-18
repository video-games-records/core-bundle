<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Repository\TeamGroupRepository;

class TeamGroupCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:team-group';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:team-group')
            ->setDescription('Command to maj team-group')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'What do you want to do?'
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
     * @param OutputInterface $output
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj-all':
                $this->majAll();
                break;
        }
        $this->end($output);
        return true;
    }


    private function majAll()
    {
        /** @var TeamGroupRepository $teamGroupRepository */
        $teamGroupRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:TeamGroup');

        $groups = $this->em->getRepository('VideoGamesRecordsCoreBundle:Group')->findAll();
        foreach ($groups as $group) {
            $teamGroupRepository->maj($group);
        }
    }
}
