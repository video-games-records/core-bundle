<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SerieCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:serie';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($em);
    }

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
                $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerSerie')->maj($idSerie);
                break;
        }
    }
}
