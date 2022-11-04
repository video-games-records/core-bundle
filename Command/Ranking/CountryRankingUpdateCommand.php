<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\Ranking\Write\CountryRankingHandler;
use VideoGamesRecords\CoreBundle\Service\Ranking\Write\PlayerSerieRankingHandler;

class CountryRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:country-ranking-update';

    private CountryRankingHandler $countryRankingHandler;

    public function __construct(CountryRankingHandler $countryRankingHandler)
    {
        $this->countryRankingHandler = $countryRankingHandler;
        parent::__construct();
    }

    protected function configure()
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
                $this->countryRankingHandler->handle($id);
                break;
            case 'maj-all':
                $this->countryRankingHandler->majAll();
                break;
        }
        return 0;
    }
}
