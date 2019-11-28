<?php
namespace VideoGamesRecords\CoreBundle\Command;

use ProjetNormandie\CommonBundle\Command\DefaultCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GameCommand extends DefaultCommand
{
    protected function configure()
    {
        $this
            ->setName('vgr-core:game')
            ->setDescription('Command to update game rankings for players')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'idGame',
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
                $idGame = $input->getOption('idGame');
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->maj($idGame);
                break;
            case 'add-from-csv':
                $service = $this->getContainer()->get('vgr.game');
                $service->addFromCsv();
                break;
            case 'update-from-csv':
                $service = $this->getContainer()->get('vgr.game');
                $service->updateFromCsv();
                break;
            case 'maj-rank':
                $service = $this->getContainer()->get('vgr.game');
                $service->majRank();
                break;
        }
        return true;
    }
}
