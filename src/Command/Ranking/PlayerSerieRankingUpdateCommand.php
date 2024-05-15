<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingCommandInterface;
use VideoGamesRecords\CoreBundle\Entity\Serie;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerSerieRankingHandler;
use VideoGamesRecords\CoreBundle\ValueObject\SerieStatus;

class PlayerSerieRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:player-serie-ranking-update';

    private EntityManagerInterface $em;
    private RankingCommandInterface $rankingCommand;

    public function __construct(
        EntityManagerInterface $em,
        #[Autowire(service: PlayerSerieRankingHandler::class)]
        RankingCommandInterface $rankingCommand
    ) {
        $this->em = $em;
        $this->rankingCommand = $rankingCommand;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('vgr-core:player-serie-ranking-update')
            ->setDescription('Command to update players ranking')
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
        $series = $this->em->getRepository(Serie::class)->findBy(['status' => SerieStatus::ACTIVE]);
        /** @var Serie $serie */
        foreach ($series as $serie) {
            $this->rankingCommand->handle($serie->getId());
        }

        return Command::SUCCESS;
    }
}
