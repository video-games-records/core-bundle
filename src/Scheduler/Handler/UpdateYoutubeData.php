<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Scheduler\Handler;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Video;
use VideoGamesRecords\CoreBundle\Handler\Video\YoutubeDataHandler;
use VideoGamesRecords\CoreBundle\Scheduler\Message\UpdatePlayerChartRanking;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use VideoGamesRecords\CoreBundle\ValueObject\VideoType;

#[AsMessageHandler]
class UpdateYoutubeData
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly YoutubeDataHandler $handler
    ) {
    }

    public function __invoke(UpdatePlayerChartRanking $message): void
    {
        $videos = $this->em->getRepository(Video::class)->findBy(
            [
                'isActive' => true,
                'type' => VideoType::YOUTUBE
            ],
            ['id' => 'DESC'],
            $message->getNb()
        );
        /** @var Video $video */
        foreach ($videos as $video) {
            $this->handler->process($video);
        }
    }
}
