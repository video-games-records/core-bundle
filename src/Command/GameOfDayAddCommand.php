<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\GameOfDayManager;

class GameOfDayAddCommand extends Command
{
    protected static $defaultName = 'vgr-core:game-of-day-add';

    private GameOfDayManager $gameOfDayManager;

    public function __construct( GameOfDayManager $gameOfDayManager)
    {
        $this->gameOfDayManager = $gameOfDayManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:game-of-day-add')
            ->setDescription('Command to add game of day')
        ;
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws ORMException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->gameOfDayManager->add();
        return 0;
    }
}
