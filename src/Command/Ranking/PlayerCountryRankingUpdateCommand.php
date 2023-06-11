<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Handler\Ranking\Player\PlayerCountryRankingHandler;

class PlayerCountryRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:country-ranking-update';

    private PlayerCountryRankingHandler $playerCountryRankingHandler;

    public function __construct(PlayerCountryRankingHandler $playerCountryRankingHandler)
    {
        $this->playerCountryRankingHandler = $playerCountryRankingHandler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('vgr-core:country-ranking-update')
            ->setDescription('Command to update players ranking')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
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
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj':
                $id = $input->getOption('id');
                $this->playerCountryRankingHandler->handle($id);
                break;
            case 'maj-all':
                $this->playerCountryRankingHandler->majAll();
                break;
        }
        return Command::SUCCESS;
    }
}
