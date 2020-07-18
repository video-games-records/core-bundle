<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use VideoGamesRecords\CoreBundle\Entity\User\UserInterface;

interface ArticleInterface
{
    /** @return string */
    public function getTitle();
    /** @return string */
    public function gettext();
    /** @return string */
    public function getStatus();
    /** @return UserInterface */
    public function getAuhor();
}
