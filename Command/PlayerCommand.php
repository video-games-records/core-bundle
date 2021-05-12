<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\PlayerService;

class PlayerCommand extends Command
{
    protected static $defaultName = 'vgr-core:player';

    private $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
        parent::__construct();
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
            );
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj':
                $this->playerService->maj();
                break;
            case 'maj-rank-badge':
                $this->playerService->majRankBadge();
                break;
            /*case 'maj-rank-country':
                $country = $this->em->getRepository('VideoGamesRecordsCoreBundle:CountryInterface')->find($input->getOption('idCountry'));
                $this->em->getRepository('VideoGamesRecordsCoreBundle:Player')->majRankCountry($country);
                $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerBadge')->majCountryBadge($country);
                break;*/

            case 'maj-rules-of-three':
                $this->playerService->majRulesOfThree();
                break;
        }
        return 0;
    }
}
