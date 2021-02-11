<?php
namespace VideoGamesRecords\CoreBundle\Command;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Repository\VideoRepository;

class VideoCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:video';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:video')
            ->setDescription('Command videos')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'debug',
                null,
                InputOption::VALUE_NONE,
                ''
            )
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return bool
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);
        $function = $input->getArgument('function');
        switch ($function) {
            case 'migrate':
                $this->migrate();
                break;
        }
        $this->end($output);
        return true;
    }

    /**
     *
     */
    private function migrate()
    {
        /** @var VideoRepository $videoRepository */
        $videoRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Video');

        $videos = $videoRepository->findAll();
        foreach ($videos as $video) {
            if ($video->getUrl() != null) {
                $video->majTypeAndVideoId();
            }
        }
        $this->em->flush();
    }
}
