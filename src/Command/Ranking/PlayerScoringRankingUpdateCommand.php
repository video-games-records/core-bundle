<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;
use VideoGamesRecords\CoreBundle\Handler\Ranking\Player\PlayerScoringRankingHandler;

class PlayerScoringRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:player-scoring-ranking-update';

    private PlayerScoringRankingHandler $playerScoringRankingHandler;

    public function __construct(PlayerScoringRankingHandler $playerScoringRankingHandler)
    {
        $this->playerScoringRankingHandler = $playerScoringRankingHandler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('vgr-core:scoring-player-ranking-update')
            ->setDescription('Command to update all players rankings after scroring')
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

        if ($lock->acquire()) {
            $this->playerScoringRankingHandler->handle();
            $lock->release();
        } else {
            echo self::$defaultName . " IS LOCKED\n";
        }
        return Command::SUCCESS;
    }
}
