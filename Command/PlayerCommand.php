<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PlayerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vgr:player')
            ->setDescription('Greet someone')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'idPlayer',
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
            case 'maj':
                $idPlayer = $input->getOption('idPlayer');
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Player')->maj($idPlayer);
                break;
            case 'maj-rank-point-chart':
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankPointChart();
                break;
            case 'maj-rank-medal':
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankMedal();
                break;
            case 'maj-rank-cup':
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankCup();
                break;
            case 'maj-rank-proof':
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankProof();
                break;
        }
        return true;
    }
}
