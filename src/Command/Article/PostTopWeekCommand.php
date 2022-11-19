<?php
namespace VideoGamesRecords\CoreBundle\Command\Article;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\Article\PostTopWeekHandler;

class PostTopWeekCommand extends Command
{
    protected static $defaultName = 'vgr-core:post-top-week';

    private PostTopWeekHandler $postTopWeekHandler;

    public function __construct(PostTopWeekHandler $postTopWeekHandler)
    {
        $this->postTopWeekHandler = $postTopWeekHandler;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:post-top-week')
            ->setDescription('Command post top week')
            ->addOption(
                'date',
                null,
                InputOption::VALUE_OPTIONAL,
                ''
            )
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = $input->getOption('date');
        if ($date === null) {
            $date = date('Y-m-d');
        }
        $this->postTopWeekHandler->handle($date);
        return Command::SUCCESS;
    }
}
