<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Service\GameService;

class GameCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:game';

    private EntityManagerInterface $em;
    private GameService $gameService;

    public function __construct(EntityManagerInterface $em, GameService $gameService)
    {
        $this->gameService = $gameService;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:game')
            ->setDescription('Command to update game rankings for players')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
        ;
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws ORMException
     * @throws ExceptionInterface
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj-player':
                $idGame = $input->getOption('idGame');
                $this->gameService->majPlayerGame($idGame);
                break;
            case 'maj-team':
                $idGame = $input->getOption('idGame');
                $this->gameService->majTeamGame($idGame);
                break;
            case 'maj-master-badge':
                $idGame = $input->getOption('idGame');
                $this->gameService->majMasterBadge($idGame);
                break;
            case 'game-of-day':
                $this->gameService->addGameOfDay();
                break;
        }
        return 0;
    }
}
