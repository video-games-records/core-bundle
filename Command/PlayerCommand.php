<?php
namespace VideoGamesRecords\CoreBundle\Command;

use ProjetNormandie\CommonBundle\Command\DefaultCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PlayerCommand extends DefaultCommand
{
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
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);
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
            case 'maj-rank-point-game':
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankPointGame();
                break;
            case 'maj-rank-cup':
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankCup();
                break;
            case 'maj-rank-proof':
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankProof();
                break;
            case 'maj-rank-game':
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankGame();
                break;
            case 'maj-rank-country':
                $country = $this->getContainer()->get('doctrine')->getRepository('ProjetNormandieCountryBundle:Country')->find($input->getOption('idCountry'));
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankCountry($country);
                $this->getContainer()->get('doctrine')->getRepository('VideoGamesRecordsCoreBundle:PlayerBadge')->majCountryBadge($country);
                break;
            case 'maj-role-player':
                $this->majRolePlayer($output);
                break;
        }
        $this->end($output);
        return true;
    }

    /**
     * @param OutputInterface $output
     * @throws \Exception
     */
    private function majRolePlayer(OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        /** @var \VideoGamesRecords\CoreBundle\Repository\PlayerRepository $playerRepository */
        $playerRepository = $em->getRepository('VideoGamesRecordsCoreBundle:Player');

        $group = $em->getReference('VideoGamesRecords\CoreBundle\Entity\User\GroupInterface', 2);

        $players = $playerRepository->getPlayerToDisabled();
        foreach ($players as $player) {
            $user = $player->getUser();
            $user->removeGroup($group);
        }
        $em->flush();
        $output->writeln(sprintf('%d players(s) disabled', count($players)));


        $players = $playerRepository->getPlayerToEnabled();
        foreach ($players as $player) {
            $user = $player->getUser();
            $user->addGroup($group);
        }
        $em->flush();
        $output->writeln(sprintf('%d players(s) enabled', count($players)));
    }
}
