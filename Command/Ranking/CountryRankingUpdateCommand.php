<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\Ranking\Updater\CountryRankingUpdater;
use VideoGamesRecords\CoreBundle\Service\Ranking\Updater\PlayerSerieRankingUpdater;

class CountryRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:country-ranking-update';

    private CountryRankingUpdater $countryRankingUpdater;

    public function __construct(CountryRankingUpdater $countryRankingUpdater)
    {
        $this->countryRankingUpdater = $countryRankingUpdater;
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
                $this->countryRankingUpdater->maj($id);
                break;
            case 'maj-all':
                $this->countryRankingUpdater->majAll();
                break;
        }
        return 0;
    }
}
