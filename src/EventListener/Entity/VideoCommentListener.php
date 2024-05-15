<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\VideoComment;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

class VideoCommentListener
{
    private UserProvider $userProvider;

    /**
     * @param UserProvider $userProvider
     */
    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param VideoComment       $comment
     * @param LifecycleEventArgs $event
     */
    public function prePersist(VideoComment $comment, LifecycleEventArgs $event): void
    {
        $comment->setPlayer($this->userProvider->getPlayer());
        $comment->getVideo()->setNbComment($comment->getVideo()->getNbComment() + 1);
    }


    /**
     * @param VideoComment       $comment
     * @param LifecycleEventArgs $event
     */
    public function preRemove(VideoComment $comment, LifecycleEventArgs $event): void
    {
        $comment->getVideo()->setNbComment($comment->getVideo()->getNbComment() - 1);
    }
}
