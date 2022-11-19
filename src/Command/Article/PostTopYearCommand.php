<?php
namespace VideoGamesRecords\CoreBundle\Command\Article;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Service\Article\PostTopYearHandler;

class PostTopYearCommand extends Command
{
    protected static $defaultName = 'vgr-core:post-top-year';

    private PostTopYearHandler $postTopYearHandler;

    public function __construct(PostTopYearHandler $postTopYearHandler)
    {
        $this->postTopYearHandler = $postTopYearHandler;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:post-top-year')
            ->setDescription('Command post top year')
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
        $this->postTopYearHandler->handle($date);
        return Command::SUCCESS;
    }
}
