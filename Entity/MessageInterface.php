<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use VideoGamesRecords\CoreBundle\Entity\User\UserInterface;

interface MessageInterface
{
    /** @return string */
    public function getObject();
    /** @return string */
    public function getMessage();
    /** @return integer */
    public function getType();
    /** @return UserInterface */
    public function getSender();
    /** @return UserInterface */
    public function getRecipient();
}
