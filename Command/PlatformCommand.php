<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Service\PlatformService;

class PlatformCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:platform';

    private PlatformService $platformService;

    public function __construct(EntityManagerInterface $em, PlatformService $platformService)
    {
        $this->platformService = $platformService;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:platform')
            ->setDescription('Platform commands')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'idPlatform',
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
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input);
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj-all':
                $this->platformService->majAll();
                break;
            case 'maj':
                $this->platformService->majRanking($input->getOption('idPlatform'));
                break;
        }
        $this->end($output);
        return 0;
    }
}
