<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\VideoComment;

class VideoCommentListener
{

    /**
     * @param VideoComment       $comment
     * @param LifecycleEventArgs $event
     */
    public function prePersist(VideoComment $comment, LifecycleEventArgs $event): void
    {
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
