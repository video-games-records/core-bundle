<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankUpdateInterface;
use VideoGamesRecords\CoreBundle\Ranking\Command\RankUpdate\PlayerRankUpdateHandler;

#[AsCommand(
    name: 'vgr-core:player-ranking-proof-update',
    description: 'Command to update players ranking'
)]
class PlayerRankingProofUpdateCommand extends Command
{
    private RankUpdateInterface $rankUpdate;

    public function __construct(
        #[Autowire(service: PlayerRankUpdateHandler::class)]
        RankUpdateInterface $rankUpdate
    ) {
        $this->rankUpdate = $rankUpdate;
        parent::__construct();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->rankUpdate->majRankProof();
        return Command::SUCCESS;
    }
}
