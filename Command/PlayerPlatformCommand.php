<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

class PlayerPlatformCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:player-platform';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:player-platform')
            ->setDescription('Command to update platform rankings for players')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'idPlatform',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return bool|int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj-all':
                $platforms = $this->em->getRepository('VideoGamesRecordsCoreBundle:Platform')->findAll();
                foreach ($platforms as $platform) {
                    $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerPlatform')->maj($platform);
                }
                break;
            case 'maj':
                $idPlatform = $input->getOption('idPlatform');
                $platform = $this->em->getRepository('VideoGamesRecordsCoreBundle:Platform')->find($idPlatform);
                $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerPlatform')->maj($platform);
                break;
        }
        return true;
    }
}
