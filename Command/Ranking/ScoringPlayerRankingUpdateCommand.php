<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;
use VideoGamesRecords\CoreBundle\Service\Ranking\Updater\ScoringPlayerRankingUpdater;

class ScoringPlayerRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:scoring-player-ranking-update';

    private ScoringPlayerRankingUpdater $scoringPlayerRankingUpdater;

    public function __construct(ScoringPlayerRankingUpdater $scoringPlayerRankingUpdater)
    {
        $this->scoringPlayerRankingUpdater = $scoringPlayerRankingUpdater;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:scoring-player-ranking-update')
            ->setDescription('Command to update all players rankings after scroring')
            ->addOption(
                'id',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
        ;
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $store = new SemaphoreStore();
        $factory = new LockFactory($store);

        $lock = $factory->createLock(self::$defaultName);

        if ($lock->acquire()) {
            $this->scoringPlayerRankingUpdater->process();
            $lock->release();
        }
        return 0;
    }
}
