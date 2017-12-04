<?php
namespace VideoGamesRecords\CoreBundle\Command;

use ProjetNormandie\CommonBundle\Command\DefaultCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SerieCommand extends DefaultCommand
{
    protected function configure()
    {
        $this
            ->setName('vgr-core:serie')
            ->setDescription('Command to update serie rankings for players')
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
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerSerie')->maj($idSerie);
                break;
        }
    }
}
