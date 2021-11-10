<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\CountryService;

class CountryCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:country';

    private EntityManagerInterface $em;
    private CountryService $countryService;

    public function __construct(EntityManagerInterface $em, CountryService $countryService)
    {
        $this->countryService = $countryService;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:player')
            ->setDescription('Command to update players ranking')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
             ->addOption(
                'debug',
                'd',
                InputOption::VALUE_NONE,
                'Debug option (sql)'
            );
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input);
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj':
                $this->countryService->maj();
                break;
        }
        $this->end($output);
        return 0;
    }
}
