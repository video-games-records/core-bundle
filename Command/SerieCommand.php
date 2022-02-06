<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Service\SerieService;

class SerieCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:serie';

    private SerieService $serieService;

    public function __construct(EntityManagerInterface $em, SerieService $serieService)
    {
        $this->serieService = $serieService;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:serie')
            ->setDescription('Command to update serie rankings for players')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'idSerie',
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
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input);
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj':
                $idSerie = $input->getOption('idSerie');
                $this->serieService->maj($idSerie);
                break;
        }
        $this->end($output);
        return 0;
    }
}
