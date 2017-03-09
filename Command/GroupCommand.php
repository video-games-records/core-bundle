<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GroupCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vgr:group')
            ->setDescription('Command to update group rankings for players and teams')
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
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->maj($idGroup);
                break;
            case 'maj-team':
                $idGroup = $input->getOption('idGroup');
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:TeamGroup')->maj($idGroup);
                break;
        }

        return true;
    }
}
