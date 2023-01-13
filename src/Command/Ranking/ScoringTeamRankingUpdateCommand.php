<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;
use VideoGamesRecords\CoreBundle\Service\Ranking\Write\ScoringTeamRankingHandler;

class ScoringTeamRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:scoring-team-ranking-update';

    private ScoringTeamRankingHandler $scoringTeamRankingHandler;

    public function __construct(ScoringTeamRankingHandler $scoringTeamRankingHandler)
    {
        $this->scoringTeamRankingHandler = $scoringTeamRankingHandler;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:scoring-team-ranking-update')
            ->setDescription('Command to update all team rankings after scroring')
            ->addArgument(
                'release',
                InputArgument::OPTIONAL,
                'Who do you want to do?'
            )
        ;
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $store = new SemaphoreStore();
        $factory = new LockFactory($store);

        $lock = $factory->createLock(self::$defaultName);

        $release = $input->getArgument('release');
        if ($release) {
             echo self::$defaultName . " IS RELEASED\n";
             $lock->release();
        }

        if ($lock->acquire()) {
            $this->scoringTeamRankingHandler->handle();
            $lock->release();
        } else {
            echo self::$defaultName . " IS LOCKED\n";
        }
        return Command::SUCCESS;
    }
}
