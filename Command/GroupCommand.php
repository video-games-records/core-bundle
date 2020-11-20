<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

class GroupCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:group';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj-player':
                $idGroup = $input->getOption('idGroup');
                $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->maj($idGroup);
                break;
            case 'maj-team':
                $idGroup = $input->getOption('idGroup');
                $this->em->getRepository('VideoGamesRecordsCoreBundle:TeamGroup')->maj($idGroup);
                break;
        }

        return true;
    }
}
