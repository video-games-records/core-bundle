<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SerieCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vgr:serie')
            ->setDescription('Greet someone')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'idSerie',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj':
                $idSerie = $input->getOption('idSerie');
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:UserSerie')->maj($idSerie);
                break;
        }

        /*$output->writeln($function);
        $output->writeln($idSerie);*/
    }
}
