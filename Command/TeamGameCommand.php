<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Repository\TeamGameRepository;

class TeamGameCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:team-game';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:team-game')
            ->setDescription('Command to maj team-game')
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
        /** @var TeamGameRepository $teamGameRepository */
        $teamGameRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:TeamGame');

        $games = $this->em->getRepository('VideoGamesRecordsCoreBundle:Game')->findAll();
        foreach ($games as $game) {
            $teamGameRepository->maj($game);
        }
    }
}
