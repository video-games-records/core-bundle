<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingCommandInterface;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerPlatformRankingHandler;

#[AsCommand(
    name: 'vgr-core:player-platform-ranking-update',
    description: 'Command to update players ranking'
)]
class PlayerPlatformRankingUpdateCommand extends Command
{
    private EntityManagerInterface $em;
    private RankingCommandInterface $rankingCommand;

    public function __construct(
        EntityManagerInterface $em,
        #[Autowire(service: PlayerPlatformRankingHandler::class)]
        RankingCommandInterface $rankingCommand
    ) {
        $this->em = $em;
        $this->rankingCommand = $rankingCommand;
        parent::__construct();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $platforms = $this->em->getRepository(Platform::class)->findAll();
        /** @var Platform $platform */
        foreach ($platforms as $platform) {
            $this->rankingCommand->handle($platform->getId());
        }

        return Command::SUCCESS;
    }
}
