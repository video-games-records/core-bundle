<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;
use VideoGamesRecords\CoreBundle\Service\Ranking\Write\ScoringPlayerRankingHandler;

class ScoringPlayerRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:scoring-player-ranking-update';

    private ScoringPlayerRankingHandler $scoringPlayerRankingHandler;

    public function __construct(ScoringPlayerRankingHandler $scoringPlayerRankingHandler)
    {
        $this->scoringPlayerRankingHandler = $scoringPlayerRankingHandler;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:scoring-player-ranking-update')
            ->setDescription('Command to update all players rankings after scroring')
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
            $lock->release();
        }

        if ($lock->acquire()) {
            $this->scoringPlayerRankingHandler->handle();
            $lock->release();
        } else {
            echo self::$defaultName . " IS LOCKED\n";
        }
        return Command::SUCCESS;
    }
}
