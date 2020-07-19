<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TeamCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:team';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:team')
            ->setDescription('Command to update teams ranking')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'idTeam',
                null,
                InputOption::VALUE_REQUIRED,
                ''
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
                $idTeam = $input->getOption('idTeam');
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Team')->maj($idTeam);
                break;
            case 'maj-rank-point-chart':
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Team')->majRankPointChart();
                break;
            case 'maj-rank-medal':
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Team')->majRankMedal();
                break;
            case 'maj-rank-point-game':
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Team')->majRankPointGame();
                break;
            case 'maj-rank-cup':
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Team')->majRankCup();
                break;
            case 'maj-game-rank':
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Team')->majGameRank();
                break;
            case 'maj-nb-master-badge':
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Team')->majNbMasterBadge();
                break;
        }
        $this->end($output);
        return true;
    }
}
