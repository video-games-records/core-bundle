<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;
use VideoGamesRecords\CoreBundle\Ranking\Command\ScoringPlayerRankingHandler;

#[AsCommand(
    name: 'vgr-core:scoring-player-ranking-update',
    description: 'Command to update all players rankings after scoring'
)]
class PlayerScoringRankingUpdateCommand extends Command
{
    private ScoringPlayerRankingHandler $scoringPlayerRankingHandler;

    public function __construct(ScoringPlayerRankingHandler $scoringPlayerRankingHandler)
    {
        $this->scoringPlayerRankingHandler = $scoringPlayerRankingHandler;
        parent::__construct();
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

        $lock = $factory->createLock($this->getName());

        if ($lock->acquire()) {
            $this->scoringPlayerRankingHandler->handle();
            $lock->release();
        } else {
            echo self::$defaultName . " IS LOCKED\n";
        }
        return Command::SUCCESS;
    }
}
