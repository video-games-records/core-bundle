<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;

class PlayerCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:player';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:player')
            ->setDescription('Command to update players ranking')
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
            ->addOption(
                'idCountry',
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
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj':
                $idPlayer = $input->getOption('idPlayer');
                $player = $this->em->getRepository('VideoGamesRecordsCoreBundle:Player')->find($idPlayer);
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Player')->maj($player);
                break;
            case 'maj-game-rank':
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Player')->majGameRank();
                break;
            case 'maj-rank-point-chart':
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankPointChart();
                break;
            case 'maj-rank-medal':
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankMedal();
                break;
            case 'maj-rank-point-game':
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankPointGame();
                break;
            case 'maj-rank-cup':
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankCup();
                break;
            case 'maj-rank-proof':
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankProof();
                break;
            case 'maj-rank-country':
                $country = $this->em->getRepository('VideoGamesRecordsCoreBundle:CountryInterface')->find($input->getOption('idCountry'));
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankCountry($country);
                $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerBadge')->majCountryBadge($country);
                break;
            case 'maj-nb-master-badge':
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Player')->majNbMasterBadge();
                break;
            case 'maj-rules-of-three':
                $this->majRulesOfThree($output);
                break;
        }
        $this->end($output);
        return true;
    }

    /**
     * @param OutputInterface $output
     * @throws Exception
     */
    private function majRulesOfThree(OutputInterface $output)
    {
        /** @var PlayerRepository $playerRepository */
        $playerRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Player');

        $group1 = $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\User\GroupInterface', 2);
        $group2 = $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\User\GroupInterface', 9);

        $players = $playerRepository->getPlayerToDisabled();
        foreach ($players as $player) {
            $user = $player->getUser();
            $user->removeGroup($group1);
            $user->addGroup($group2);
        }
        $this->em->flush();
        $output->writeln(sprintf('%d players(s) disabled', count($players)));


        $players = $playerRepository->getPlayerToEnabled();
        foreach ($players as $player) {
            $user = $player->getUser();
            $user->addGroup($group1);
            $user->removeGroup($group2);
        }
        $this->em->flush();
        $output->writeln(sprintf('%d players(s) enabled', count($players)));
    }
}
