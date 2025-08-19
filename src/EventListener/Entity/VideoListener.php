<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\DataProvider\YoutubeProvider;
use VideoGamesRecords\CoreBundle\Entity\Video;
use VideoGamesRecords\CoreBundle\Security\UserProvider;
use VideoGamesRecords\CoreBundle\ValueObject\VideoType;

class VideoListener
{
    private UserProvider $userProvider;
    private YoutubeProvider $youtubeProvider;
    private TranslatorInterface $translator;

    /**
     * @param UserProvider        $userProvider
     * @param YoutubeProvider     $youtubeProvider
     * @param TranslatorInterface $translator
     */
    public function __construct(
        UserProvider $userProvider,
        YoutubeProvider $youtubeProvider,
        TranslatorInterface $translator
    ) {
        $this->userProvider = $userProvider;
        $this->youtubeProvider = $youtubeProvider;
        $this->translator = $translator;
    }

    /**
     * @param Video              $video
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function prePersist(Video $video, LifecycleEventArgs $event): void
    {
        $video->setPlayer($this->userProvider->getPlayer());
        $video->getPlayer()->setNbVideo($video->getPlayer()->getNbVideo() + 1);

        $video->getGame()
            ?->setNbVideo(
                $video->getGame()
                    ->getNbVideo() + 1
            );

        // Set youtube data
        if ($video->getVideoType()->getValue() === VideoType::YOUTUBE) {
            $response = $this->youtubeProvider->getVideo($video->getExternalId());

            if (count($response->getItems()) == 0) {
                throw new BadRequestException($this->translator->trans('video.youtube.not_found'));
            }

            $youtubeVideo = $response->getItems()[0];

            $snippet = $youtubeVideo->getSnippet();
            $video->setTitle($snippet->getTitle());
            $video->setThumbnail($snippet->getThumbnails()->getHigh()->getUrl());

            $video->setDescription($snippet->getDescription());

            $statistics = $youtubeVideo->getStatistics();
            $video->setLikeCount((int) $statistics->getLikeCount());
            $video->setViewCount((int) $statistics->getViewCount());
        }
    }

    /**
     * @param Video              $video
     * @param LifecycleEventArgs $event
     */
    public function preRemove(Video $video, LifecycleEventArgs $event): void
    {
        $video->getPlayer()->setNbVideo($video->getPlayer()->getNbVideo() - 1);

        $video->getGame()
            ?->setNbVideo(
                $video->getGame()
                    ->getNbVideo() - 1
            );
    }
}
