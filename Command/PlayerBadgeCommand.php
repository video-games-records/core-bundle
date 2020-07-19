<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

class PlayerBadgeCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:player-badge';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:player-badge')
            ->setDescription('Command to maj player badges')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'debug',
                null,
                InputOption::VALUE_NONE,
                ''
            )
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj':
                $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerBadge')->majUserBadge();
                $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerBadge')->majPlayerBadge();
                break;
        }
        $this->end($output);
        return true;
    }
}
