<?php

namespace VideoGamesRecords\CoreBundle\Command\Video;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Handler\Video\YoutubeDataHandler;
use VideoGamesRecords\CoreBundle\Entity\Video;
use VideoGamesRecords\CoreBundle\ValueObject\VideoType;

class YoutubeDataUpdate extends Command
{
    protected static $defaultName = 'vgr-core:video-youtube-data-update';

    private EntityManagerInterface $em;
    private YoutubeDataHandler $youtubeDataHandler;

    public function __construct(EntityManagerInterface $em, YoutubeDataHandler $youtubeDataHandler)
    {
        $this->em = $em;
        $this->youtubeDataHandler = $youtubeDataHandler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('vgr-core:video-youtube-data-update')
            ->setDescription('Command to update videos with youtube data');
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $videos = $this->em->getRepository(Video::class)->findBy(['isActive' => true, 'type' => VideoType::TYPE_YOUTUBE, 'title' => null]);
        /** @var Video $video */
        foreach ($videos as $video) {
            $this->youtubeDataHandler->process($video);
        }

        return Command::SUCCESS;
    }
}
