<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Command\Video;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Handler\Video\YoutubeDataHandler;
use VideoGamesRecords\CoreBundle\Entity\Video;
use VideoGamesRecords\CoreBundle\ValueObject\VideoType;

#[AsCommand(
    name: 'vgr-core:video-youtube-data-update',
    description: 'Command to update videos with youtube data'
)]
class YoutubeDataUpdate extends Command
{
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
            ->addOption(
                'nb',
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
        $videos = $this->em->getRepository(Video::class)->findBy(
            [
                'isActive' => true,
                'type' => VideoType::YOUTUBE
            ],
            ['id' => 'DESC'],
            $input->getOption('nb') ?? null
        );
        /** @var Video $video */
        foreach ($videos as $video) {
            $this->youtubeDataHandler->process($video);
        }

        return Command::SUCCESS;
    }
}
